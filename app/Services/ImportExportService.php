<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LoyaltyWallet;
use App\Models\Visit;
use App\Models\AuditLog;
use App\Traits\HasPhoneNormalization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VisitsExport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportService
{
    use HasPhoneNormalization;

    /**
     * Import customers from array data
     */
    public function importFromArray(array $data, string $mode = 'insert', ?int $userId = null, ?int $outletId = null): array
    {
        $results = [
            'inserted' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        foreach ($data as $index => $row) {
            try {
                $result = $this->processRow($row, $mode, $userId, $outletId);
                
                switch ($result['action']) {
                    case 'inserted':
                        $results['inserted']++;
                        break;
                    case 'updated':
                        $results['updated']++;
                        break;
                    case 'skipped':
                        $results['skipped']++;
                        break;
                }
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'row' => $index + 1,
                    'error' => $e->getMessage(),
                    'data' => $row,
                ];
            }
        }

        // Log export/import
        AuditLog::log(
            $userId,
            'import_customers',
            'Customer',
            null,
            null,
            [
                'mode' => $mode,
                'total_rows' => count($data),
                'results' => $results,
            ]
        );

        return $results;
    }

    /**
     * Process a single row
     */
    protected function processRow(array $row, string $mode, ?int $userId, ?int $outletId): array
    {
        // Normalize phone
        $mobileJson = null;
        if (!empty($row['phone_country_code']) && !empty($row['phone_number'])) {
            $mobileJson = self::normalizePhone($row['phone_country_code'], $row['phone_number']);
            if (!$mobileJson) {
                throw new \Exception('Invalid phone number');
            }
        }

        // Find existing customer
        $email = $row['email'] ?? null;
        $mobileE164 = $mobileJson['e164'] ?? null;
        
        $existingCustomer = Customer::findByIdentity($email, $mobileE164);

        if ($existingCustomer) {
            if ($mode === 'insert') {
                return [
                    'action' => 'skipped',
                    'reason' => 'Customer already exists',
                    'customer_id' => $existingCustomer->id,
                ];
            }

            // Update existing customer
            $this->updateCustomerFromRow($existingCustomer, $row, $mobileJson, $userId);
            
            return [
                'action' => 'updated',
                'customer_id' => $existingCustomer->id,
            ];
        }

        // Create new customer
        $customer = $this->createCustomerFromRow($row, $mobileJson, $userId, $outletId);
        
        return [
            'action' => 'inserted',
            'customer_id' => $customer->id,
        ];
    }

    /**
     * Create customer from row data
     */
    protected function createCustomerFromRow(array $row, ?array $mobileJson, ?int $userId, ?int $outletId): Customer
    {
        return DB::transaction(function () use ($row, $mobileJson, $userId, $outletId) {
            $customer = Customer::create([
                'type' => $row['type'] ?? 'individual',
                'name' => $row['name'],
                'nationality' => $row['nationality'] ?? null,
                'gender' => $row['gender'] ?? 'unknown',
                'email' => $row['email'] ?? null,
                'mobile_json' => $mobileJson,
                'date_of_birth' => $this->parseDate($row['date_of_birth'] ?? null),
                'address' => $row['address'] ?? null,
                'company_name' => $row['company_name'] ?? null,
                'position' => $row['position'] ?? null,
                'first_registration_outlet_id' => $outletId,
                'created_by_user_id' => $userId,
                'status' => 'active',
            ]);

            // Create loyalty wallet
            LoyaltyWallet::create([
                'customer_id' => $customer->id,
                'total_points' => 0,
            ]);

            return $customer;
        });
    }

    /**
     * Update customer from row data
     */
    protected function updateCustomerFromRow(Customer $customer, array $row, ?array $mobileJson, ?int $userId): Customer
    {
        $updateData = [];
        
        $fillable = ['name', 'nationality', 'gender', 'date_of_birth', 'address', 'company_name', 'position'];
        
        foreach ($fillable as $field) {
            if (isset($row[$field]) && !empty($row[$field])) {
                $updateData[$field] = $row[$field];
            }
        }
        
        if ($mobileJson) {
            $updateData['mobile_json'] = $mobileJson;
        }
        
        if (!empty($updateData)) {
            $customer->update($updateData);
        }
        
        return $customer;
    }

    /**
     * Parse date from various formats
     */
    protected function parseDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Try various date formats
            $formats = [
                'Y-m-d',
                'd/m/Y',
                'm/d/Y',
                'd-m-Y',
                'm-d-Y',
                'd.m.Y',
                'Y/m/d',
            ];

            foreach ($formats as $format) {
                $parsed = \DateTime::createFromFormat($format, $date);
                if ($parsed !== false) {
                    return $parsed->format('Y-m-d');
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse date: {$date}");
        }

        return null;
    }

    /**
     * Export customers to array
     */
    public function exportToArray(\Illuminate\Database\Eloquent\Collection $customers, array $fields): array
    {
        $data = [];

        foreach ($customers as $customer) {
            $row = [];
            
            foreach ($fields as $field) {
                $row[$field] = $this->getFieldValue($customer, $field);
            }
            
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Get field value from customer
     */
    protected function getFieldValue(Customer $customer, string $field): mixed
    {
        return match ($field) {
            'id' => $customer->id,
            'type' => $customer->type,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone_country_code' => $customer->mobile_json['country_dial_code'] ?? '',
            'phone_number' => $customer->mobile_json['national_number'] ?? '',
            'phone_e164' => $customer->mobile_e164 ?? '',
            'nationality' => $customer->nationality,
            'gender' => $customer->gender,
            'date_of_birth' => $customer->date_of_birth?->format('Y-m-d'),
            'age' => $customer->age,
            'age_group' => $customer->age_group,
            'zodiac' => $customer->zodiac,
            'address' => $customer->address,
            'company_name' => $customer->company_name,
            'position' => $customer->position,
            'status' => $customer->status,
            'total_points' => $customer->loyaltyWallet?->total_points ?? 0,
            'tier' => $customer->loyaltyWallet?->tier ?? 'basic',
            'total_visits' => $customer->visits->count(),
            'total_spend' => $customer->visits->sum('bill_amount'),
            'first_registration_outlet' => $customer->firstRegistrationOutlet?->name ?? '',
            'registered_at' => $customer->created_at?->format('Y-m-d H:i:s'),
            default => '',
        };
    }

    /**
     * Get available export fields
     */
    public function getExportFields(): array
    {
        return [
            'id' => 'Customer ID',
            'type' => 'Type',
            'name' => 'Name',
            'email' => 'Email',
            'phone_country_code' => 'Phone Country Code',
            'phone_number' => 'Phone Number',
            'phone_e164' => 'Phone E.164',
            'nationality' => 'Nationality',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of Birth',
            'age' => 'Age',
            'age_group' => 'Age Group',
            'zodiac' => 'Zodiac Sign',
            'address' => 'Address',
            'company_name' => 'Company Name',
            'position' => 'Position',
            'status' => 'Status',
            'total_points' => 'Total Points',
            'tier' => 'Loyalty Tier',
            'total_visits' => 'Total Visits',
            'total_spend' => 'Total Spend',
            'first_registration_outlet' => 'First Registration Outlet',
            'registered_at' => 'Registered At',
        ];
    }

    /**
     * Get import template fields
     */
    public function getImportTemplateFields(): array
    {
        return [
            'name' => 'Name (required)',
            'email' => 'Email',
            'phone_country_code' => 'Phone Country Code (e.g., BH)',
            'phone_number' => 'Phone Number',
            'type' => 'Type (individual/corporate)',
            'nationality' => 'Nationality',
            'gender' => 'Gender (male/female/other)',
            'date_of_birth' => 'Date of Birth (Y-m-d)',
            'address' => 'Address',
            'company_name' => 'Company Name',
            'position' => 'Position',
        ];
    }

    /**
     * Validate import data
     */
    public function validateImportData(array $data): array
    {
        $errors = [];
        
        foreach ($data as $index => $row) {
            // Required fields
            if (empty($row['name'])) {
                $errors[] = "Row " . ($index + 1) . ": Name is required";
            }
            
            // Email format
            if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row " . ($index + 1) . ": Invalid email format";
            }
            
            // Phone validation
            if (!empty($row['phone_country_code']) && !empty($row['phone_number'])) {
                $normalized = self::normalizePhone($row['phone_country_code'], $row['phone_number']);
                if (!$normalized) {
                    $errors[] = "Row " . ($index + 1) . ": Invalid phone number";
                }
            }
            
            // Date validation
            if (!empty($row['date_of_birth'])) {
                try {
                    \DateTime::createFromFormat('Y-m-d', $row['date_of_birth']);
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": Invalid date format (use Y-m-d)";
                }
            }
        }
        
        return $errors;
    }

    /**
     * Export visits to file
     */
    public function exportVisits(array $filters = [], string $format = 'csv'): StreamedResponse
    {
        $filename = 'visits_export_' . now()->format('Y-m-d_H-i-s');
        
        return Excel::download(
            new VisitsExport($filters),
            "{$filename}.{$format}"
        );
    }

    /**
     * Get visits for export (used by the export class)
     */
    public function getVisitsForExport(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Visit::with(['customer', 'outlet', 'staff']);

        // Filter by outlet
        if (!empty($filters['outlet_id'])) {
            $query->where('outlet_id', $filters['outlet_id']);
        }

        // Filter by customer
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        // Date range
        if (!empty($filters['start_date'])) {
            $query->whereDate('visited_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('visited_at', '<=', $filters['end_date']);
        }

        // Search on customer name, email, mobile
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('mobile_json', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('visited_at', 'desc')->get();
    }

    /**
     * Get available export fields for visits
     */
    public function getVisitExportFields(): array
    {
        return [
            'id' => 'Visit ID',
            'customer_name' => 'Customer Name',
            'customer_email' => 'Customer Email',
            'customer_mobile' => 'Customer Mobile',
            'outlet_name' => 'Outlet',
            'staff_name' => 'Staff',
            'bill_amount' => 'Bill Amount',
            'points_awarded' => 'Points Awarded',
            'visit_type' => 'Visit Type',
            'visited_at' => 'Visit Date & Time',
            'notes' => 'Notes',
        ];
    }
}


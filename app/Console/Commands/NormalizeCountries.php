<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:normalize-countries {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize country data to ISO2 codes across all tables';

    /**
     * Country name to ISO2 code mapping
     */
    private array $countryMapping = [
        'Bahrain' => 'BH',
        'Saudi Arabia' => 'SA',
        'United Arab Emirates' => 'AE',
        'UAE' => 'AE',
        'Kuwait' => 'KW',
        'Qatar' => 'QA',
        'Oman' => 'OM',
        'Egypt' => 'EG',
        'Jordan' => 'JO',
        'India' => 'IN',
        'Pakistan' => 'PK',
        'Philippines' => 'PH',
        'United States' => 'US',
        'USA' => 'US',
        'United Kingdom' => 'GB',
        'UK' => 'GB',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Country Data Normalization');
        $this->info('============================');
        $this->info('');
        
        // Process outlets table
        $outletsCount = $this->processOutlets($dryRun);
        
        // Process customers table - validate nationalities
        $customersCount = $this->processCustomers($dryRun);
        
        $this->info('');
        $this->info('Summary:');
        $this->info("- Outlets to update: {$outletsCount}");
        $this->info("- Customers to validate: {$customersCount}");
        
        if ($dryRun) {
            $this->warn('DRY RUN - No changes were made.');
            $this->info('Run without --dry-run to apply changes.');
        } else {
            $this->info('Data normalization complete!');
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Process outlets table
     */
    private function processOutlets(bool $dryRun): int
    {
        $count = 0;
        
        foreach ($this->countryMapping as $countryName => $iso2) {
            $outlets = DB::table('outlets')
                ->where('country', $countryName)
                ->get();
                
            foreach ($outlets as $outlet) {
                $count++;
                if (!$dryRun) {
                    DB::table('outlets')
                        ->where('id', $outlet->id)
                        ->update(['country' => $iso2]);
                }
            }
        }
        
        if ($count > 0) {
            $this->info("Found {$count} outlets with country names to convert.");
        } else {
            $this->info('All outlets already use ISO2 country codes.');
        }
        
        return $count;
    }
    
    /**
     * Process customers table - validate nationalities
     */
    private function processCustomers(bool $dryRun): int
    {
        $count = 0;
        
        // Check for any invalid nationality codes
        $invalidNationalities = DB::table('customers')
            ->whereNotNull('nationality')
            ->where('nationality', '!=', '')
            ->whereNotIn('nationality', array_values($this->countryMapping))
            ->distinct()
            ->pluck('nationality');
        
        if ($invalidNationalities->count() > 0) {
            $this->warn("Found customers with invalid nationality codes:");
            foreach ($invalidNationalities as $nat) {
                $this->warn("  - {$nat}");
            }
        } else {
            $this->info('All customers have valid nationality codes.');
        }
        
        return $invalidNationalities->count();
    }
}


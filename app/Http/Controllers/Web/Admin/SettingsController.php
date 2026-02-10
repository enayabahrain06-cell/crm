<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $backups = $this->getBackupList();
        return view('admin.settings.index', compact('backups'));
    }

    /**
     * Get list of existing backups
     */
    protected function getBackupList()
    {
        $backups = [];
        $path = storage_path('app/backups');

        if (!is_dir($path)) {
            return $backups;
        }

        $files = File::files($path);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $filename = $file->getFilename();
                $type = 'full';
                if (strpos($filename, 'database_') !== false) {
                    $type = 'database';
                } elseif (strpos($filename, 'files_') !== false) {
                    $type = 'files';
                }

                $backups[] = [
                    'filename' => $filename,
                    'type' => $type,
                    'size' => $this->formatBytes($file->getSize()),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        // Sort by date descending
        usort($backups, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $backups;
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Basic Settings
            'app_name' => 'nullable|string|max:255',
            'app_email' => 'nullable|email|max:255',
            'timezone' => 'nullable|string|max:50',
            'date_format' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:10',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'app_favicon' => 'nullable|image|mimes:png,ico|max:512',
            
            // Company Settings
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:50',
            'company_vat_number' => 'nullable|string|max:50',
            'company_website' => 'nullable|url|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_favicon' => 'nullable|image|mimes:png,ico|max:512',
            
            // Loyalty Program Settings
            'loyalty_program_enabled' => 'nullable|boolean',
            'points_per_currency' => 'nullable|numeric|min:0',
            'points_expiration_months' => 'nullable|integer|min:0',
            'min_points_redemption' => 'nullable|integer|min:0',
            'welcome_points' => 'nullable|integer|min:0',
            'enable_tiered_membership' => 'nullable|boolean',
            
            // Customer Settings
            'default_country' => 'nullable|string|size:2',
            'auto_assign_customer_to_outlet' => 'nullable|boolean',
            'birthday_reminder_days' => 'nullable|integer|min:0|max:365',
            'allow_duplicate_phones' => 'nullable|boolean',
            'require_phone_verification' => 'nullable|boolean',
            'default_customer_status' => 'nullable|string|in:active,inactive,pending',
            
            // Notification Settings
            'sms_provider' => 'nullable|string|in:twilio,nexmo,default',
            'email_provider' => 'nullable|string|in:smtp,sendgrid,mailgun',
            'send_sms_on_visit' => 'nullable|boolean',
            'send_email_on_visit' => 'nullable|boolean',
            
            // Notification Templates
            'welcome_sms_template' => 'nullable|string|max:500',
            'welcome_email_template' => 'nullable|string|max:1000',
            'birthday_sms_template' => 'nullable|string|max:500',
            'birthday_email_template' => 'nullable|string|max:1000',
            'visit_confirmation_sms_template' => 'nullable|string|max:500',
            'visit_confirmation_email_template' => 'nullable|string|max:1000',
            'loyalty_tier_upgrade_sms_template' => 'nullable|string|max:500',
            'loyalty_tier_upgrade_email_template' => 'nullable|string|max:1000',
            
            // Localization Settings
            'default_language' => 'nullable|string|size:2',
            'time_format' => 'nullable|string|in:12h,24h',
            'number_format' => 'nullable|string|in:dot,comma,space',
            
            // Security Settings
            'password_min_length' => 'nullable|integer|min:6|max:32',
            'session_timeout_minutes' => 'nullable|integer|min:5|max:1440',
            'enable_2fa' => 'nullable|boolean',
            'max_login_attempts' => 'nullable|integer|min:1|max:10',
            'lockout_duration_minutes' => 'nullable|integer|min:1|max:60',
            'enforce_strong_password' => 'nullable|boolean',
            
            // Audit & Logging
            'audit_logging_enabled' => 'nullable|boolean',
            'log_retention_days' => 'nullable|integer|min:1|max:365',
            'log_sensitive_data' => 'nullable|boolean',
            'log_api_calls' => 'nullable|boolean',
            
            // Backup Settings
            'auto_backup_enabled' => 'nullable|boolean',
            'backup_frequency' => 'nullable|string|in:daily,weekly,monthly',
            'backup_time' => 'nullable|date_format:H:i',
            'backup_retention' => 'nullable|integer|min:1|max:365',
            
            // Feature Toggles
            'qr_code_enabled' => 'nullable|boolean',
            'auto_greetings_enabled' => 'nullable|boolean',
            'customer_ratings_enabled' => 'nullable|boolean',
            'import_export_enabled' => 'nullable|boolean',
            'multi_outlet_mode' => 'nullable|boolean',
            'guest_checkin_enabled' => 'nullable|boolean',
            
            // Email Settings
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:ssl,tls,none',
            'email_from_address' => 'nullable|email|max:255',
            'email_from_name' => 'nullable|string|max:255',
        ]);

        // Handle file uploads
        if ($request->hasFile('app_logo')) {
            $logo = $request->file('app_logo');
            $logoName = 'logo.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/settings', $logoName);
            setting()->set('app_logo', $logoName);
        }

        if ($request->hasFile('app_favicon')) {
            $favicon = $request->file('app_favicon');
            $faviconName = 'favicon.' . $favicon->getClientOriginalExtension();
            $favicon->storeAs('public/settings', $faviconName);
            setting()->set('app_favicon', $faviconName);
        }

        // Handle company logo upload
        if ($request->hasFile('company_logo')) {
            $logo = $request->file('company_logo');
            $logoName = 'company_logo.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/settings', $logoName);
            setting()->set('company_logo', $logoName);
        }

        // Handle company favicon upload
        if ($request->hasFile('company_favicon')) {
            $favicon = $request->file('company_favicon');
            $faviconName = 'company_favicon.' . $favicon->getClientOriginalExtension();
            $favicon->storeAs('public/settings', $faviconName);
            setting()->set('company_favicon', $faviconName);
        }

        // Convert checkbox values
        $booleanFields = [
            'loyalty_program_enabled', 'enable_tiered_membership',
            'auto_assign_customer_to_outlet', 'allow_duplicate_phones',
            'require_phone_verification', 'send_sms_on_visit',
            'send_email_on_visit', 'enable_2fa', 'enforce_strong_password',
            'audit_logging_enabled', 'log_sensitive_data', 'log_api_calls',
            'qr_code_enabled', 'auto_greetings_enabled', 'customer_ratings_enabled',
            'import_export_enabled', 'multi_outlet_mode', 'guest_checkin_enabled',
        ];

        foreach ($booleanFields as $field) {
            // Set checkbox values - if not present in request, set to 'false'
            $validated[$field] = $request->has($field) ? 'true' : 'false';
        }

        // Add backup boolean to validated data
        $validated['auto_backup_enabled'] = $request->has('auto_backup_enabled') ? 'true' : 'false';

        // Save all settings
        foreach ($validated as $key => $value) {
            if (!in_array($key, ['app_logo', 'app_favicon']) && $value !== null) {
                setting()->set($key, $value);
            }
        }
        setting()->save();

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Delete a setting file (logo, favicon, etc.)
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $key = $request->key;
        $allowedFiles = ['app_logo', 'app_favicon', 'company_logo', 'company_favicon'];

        if (!in_array($key, $allowedFiles)) {
            return back()->with('error', 'Invalid file type.');
        }

        $fileName = setting($key);
        
        if ($fileName && Storage::exists('public/settings/' . $fileName)) {
            Storage::delete('public/settings/' . $fileName);
            setting()->set($key, null);
            setting()->save();
            
            return back()->with('success', ucfirst(str_replace('_', ' ', $key)) . ' deleted successfully.');
        }

        return back()->with('error', 'File not found.');
    }
}


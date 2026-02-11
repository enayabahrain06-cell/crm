<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Root route - redirect to dashboard or login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('auth.login');
})->name('home');

// Public routes (no authentication required)
Route::prefix('o/{outletCode}')->group(function () {
    // Outlet linktree page
    Route::get('links', [App\Http\Controllers\Web\PublicController::class, 'outletLinks'])->name('public.outlet.links');
});

// QR Registration page (public - no auth required)
Route::get('/register', [App\Http\Controllers\Web\PublicController::class, 'register'])->name('public.register');
Route::post('/register', [App\Http\Controllers\Web\PublicController::class, 'processRegistration'])->name('public.register.store');

// Authentication routes
require __DIR__ . '/auth.php';

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Web\DashboardController::class, 'index'])->name('dashboard');

    // Customers - Custom routes must be defined BEFORE resource routes to avoid being caught as parameters
    Route::get('customers/live-search', [App\Http\Controllers\Web\CustomerController::class, 'liveSearch'])->name('customers.live-search');
    Route::get('customers/autocomplete', [App\Http\Controllers\Web\CustomerController::class, 'autocomplete'])->name('customers.autocomplete');
    Route::resource('customers', App\Http\Controllers\Web\CustomerController::class);
    Route::get('customers/{customer}/360', [App\Http\Controllers\Web\CustomerController::class, 'show360'])->name('customers.360');
    Route::get('customers/{customer}/export-pdf', [App\Http\Controllers\Web\CustomerController::class, 'exportPdf'])->name('customers.export-pdf');
    Route::post('customers/{customer}/tags', [App\Http\Controllers\Web\CustomerController::class, 'addTag'])->name('customers.tags');
    Route::delete('customers/{customer}/tags/{tag}', [App\Http\Controllers\Web\CustomerController::class, 'removeTag'])->name('customers.tags.remove');
    Route::post('customers/{customer}/tags/update', [App\Http\Controllers\Web\CustomerController::class, 'updateTags'])->name('customers.tags.update');
    Route::post('customers/{customer}/points', [App\Http\Controllers\Web\CustomerController::class, 'adjustPoints'])->name('customers.points');

    // Visits
    Route::resource('visits', App\Http\Controllers\Web\VisitController::class);
    Route::get('visits/create/{customerId?}', [App\Http\Controllers\Web\VisitController::class, 'create'])->name('visits.create.with.customer');
    Route::post('visits/bulk-delete', [App\Http\Controllers\Web\VisitController::class, 'bulkDelete'])->name('visits.bulk-delete');
    Route::get('visits/live-search', [App\Http\Controllers\Web\VisitController::class, 'liveSearch'])->name('visits.live-search');
    Route::get('visits/export', [App\Http\Controllers\Web\VisitController::class, 'export'])->name('visits.export');
    Route::get('visits/export-pdf', [App\Http\Controllers\Web\VisitController::class, 'exportPdf'])->name('visits.export-pdf');

    // Outlets
    Route::resource('outlets', App\Http\Controllers\Web\OutletController::class);
    Route::get('outlets/{outlet}/social-links', [App\Http\Controllers\Web\OutletController::class, 'socialLinks'])->name('outlets.social-links');
    Route::put('outlets/{outlet}/social-links', [App\Http\Controllers\Web\OutletController::class, 'updateSocialLinks'])->name('outlets.social-links.update');
    Route::post('outlets/{outlet}/social-links', [App\Http\Controllers\Web\OutletController::class, 'storeSocialLink'])->name('outlets.social-links.store');
    Route::delete('outlets/{outlet}/social-links/{socialLink}', [App\Http\Controllers\Web\OutletController::class, 'destroySocialLink'])->name('outlets.social-links.destroy');
    Route::get('outlets/{outlet}/qr', [App\Http\Controllers\Web\OutletController::class, 'qrCode'])->name('outlets.qr');
    Route::get('outlets/{outlet}/users', [App\Http\Controllers\Web\OutletController::class, 'users'])->name('outlets.users');
    Route::post('outlets/{outlet}/users', [App\Http\Controllers\Web\OutletController::class, 'storeUser'])->name('outlets.users.store');
    Route::delete('outlets/{outlet}/users/{user}', [App\Http\Controllers\Web\OutletController::class, 'destroyUser'])->name('outlets.users.destroy');

    // Loyalty
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/wallets', [App\Http\Controllers\Web\LoyaltyController::class, 'index'])->name('wallets');
        Route::get('/wallets/{wallet}', [App\Http\Controllers\Web\LoyaltyController::class, 'wallet'])->name('wallets.show');
        Route::get('/rewards', [App\Http\Controllers\Web\LoyaltyController::class, 'rewards'])->name('rewards');
        Route::get('/rewards/create', [App\Http\Controllers\Web\LoyaltyController::class, 'createReward'])->name('rewards.create');
        Route::post('/rewards', [App\Http\Controllers\Web\LoyaltyController::class, 'storeReward'])->name('rewards.store');
        Route::get('/rewards/{reward}', [App\Http\Controllers\Web\LoyaltyController::class, 'showReward'])->name('rewards.show');
        Route::post('/rewards/{reward}/redeem', [App\Http\Controllers\Web\LoyaltyController::class, 'redeemReward'])->name('rewards.redeem');
        Route::delete('/rewards/{reward}', [App\Http\Controllers\Web\LoyaltyController::class, 'destroyReward'])->name('rewards.destroy');
        Route::resource('rules', App\Http\Controllers\Web\LoyaltyRuleController::class)->except(['show']);
    });

    // Campaigns
    Route::resource('campaigns', App\Http\Controllers\Web\CampaignController::class);
    Route::post('campaigns/{campaign}/send', [App\Http\Controllers\Web\CampaignController::class, 'send'])->name('campaigns.send');
    Route::post('campaigns/{campaign}/duplicate', [App\Http\Controllers\Web\CampaignController::class, 'duplicate'])->name('campaigns.duplicate');
    Route::get('campaigns/{campaign}/preview', [App\Http\Controllers\Web\CampaignController::class, 'preview'])->name('campaigns.preview');
    Route::post('campaigns/{campaign}/cancel', [App\Http\Controllers\Web\CampaignController::class, 'cancel'])->name('campaigns.cancel');
    Route::post('campaigns/{campaign}/retry-failed', [App\Http\Controllers\Web\CampaignController::class, 'retryFailed'])->name('campaigns.retry-failed');
    Route::get('campaigns/{campaign}/messages', [App\Http\Controllers\Web\CampaignController::class, 'messages'])->name('campaigns.messages');

    // Auto Greetings
    Route::resource('auto-greetings', App\Http\Controllers\Web\AutoGreetingController::class);
    Route::get('auto-greetings/{auto_greeting}/logs', [App\Http\Controllers\Web\AutoGreetingController::class, 'logs'])->name('auto-greetings.logs');
    Route::post('auto-greetings/{auto_greeting}/toggle', [App\Http\Controllers\Web\AutoGreetingController::class, 'toggle'])->name('auto-greetings.toggle');
    Route::post('auto-greetings/process', [App\Http\Controllers\Web\AutoGreetingController::class, 'processManual'])->name('auto-greetings.process');

    // Reports
    Route::get('/reports', [App\Http\Controllers\Web\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/customers', [App\Http\Controllers\Web\ReportController::class, 'customerReport'])->name('reports.customers');
    Route::get('/reports/visits', [App\Http\Controllers\Web\ReportController::class, 'visitReport'])->name('reports.visits');
    Route::get('/reports/loyalty', [App\Http\Controllers\Web\ReportController::class, 'loyaltyReport'])->name('reports.loyalty');

    // Import/Export
    Route::prefix('import-export')->name('import-export.')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\ImportExportController::class, 'index'])->name('index');
        Route::post('/import', [App\Http\Controllers\Web\ImportExportController::class, 'import'])->name('import');
        Route::post('/export', [App\Http\Controllers\Web\ImportExportController::class, 'export'])->name('export');
        Route::get('/export/download/{file}', [App\Http\Controllers\Web\ImportExportController::class, 'downloadExport'])->name('export.download');
    });

    // Admin routes (Super Admin, Group Manager, Outlet Manager, and Outlet Staff)
    Route::middleware(['role:super_admin|group_manager|outlet_manager|outlet_staff'])->prefix('admin')->name('admin.')->group(function () {
        // Users
        Route::resource('users', App\Http\Controllers\Web\Admin\UserController::class);
        Route::post('users/{user}/activate', [App\Http\Controllers\Web\Admin\UserController::class, 'activate'])->name('users.activate');
        Route::post('users/{user}/deactivate', [App\Http\Controllers\Web\Admin\UserController::class, 'deactivate'])->name('users.deactivate');

        // Roles (nested under Users for UI organization)
        Route::prefix('users/roles')->name('users.roles.')->group(function () {
            Route::get('/', [App\Http\Controllers\Web\Admin\RoleController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Web\Admin\RoleController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Web\Admin\RoleController::class, 'store'])->name('store');
            Route::get('/{role}', [App\Http\Controllers\Web\Admin\RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [App\Http\Controllers\Web\Admin\RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [App\Http\Controllers\Web\Admin\RoleController::class, 'destroy'])->name('destroy');
        });
        Route::post('roles/{role}/permissions', [App\Http\Controllers\Web\Admin\RoleController::class, 'updatePermissions'])->name('roles.permissions');

        // Settings
        Route::get('/settings', [App\Http\Controllers\Web\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [App\Http\Controllers\Web\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/file/{key}', [App\Http\Controllers\Web\Admin\SettingsController::class, 'deleteFile'])->name('settings.delete-file');

// Audit Logs
        Route::get('/audit-logs', [App\Http\Controllers\Web\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');

        // Backups
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/', [App\Http\Controllers\Web\Admin\BackupController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Web\Admin\BackupController::class, 'create'])->name('create');
            Route::get('/download/{filename}', [App\Http\Controllers\Web\Admin\BackupController::class, 'download'])->name('download');
            Route::get('/restore/{filename}', [App\Http\Controllers\Web\Admin\BackupController::class, 'restore'])->name('restore');
            Route::get('/delete/{filename}', [App\Http\Controllers\Web\Admin\BackupController::class, 'delete'])->name('delete');
        });

        // Master Data
        Route::resource('categories', App\Http\Controllers\CategoryController::class);
        Route::resource('products', App\Http\Controllers\ProductController::class);
    });
});

// Legacy route redirects for backward compatibility
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::redirect('roles', 'users/roles')->name('admin.roles.index');
    Route::redirect('roles/{any}', 'users/roles/{any}')->where('any', '.*')->name('admin.roles.redirect');
});

// API routes
Route::prefix('api/v1')->middleware(['api'])->group(function () {
    // Health check
    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    });

    // Public endpoints
    Route::get('/outlets/{code}/links', [App\Http\Controllers\Web\PublicController::class, 'apiOutletLinks']);

    // Authenticated API endpoints
    Route::middleware(['auth:api'])->group(function () {
        // Customers
        Route::get('/customers', [App\Http\Controllers\Api\CustomerController::class, 'index']);
        Route::get('/customers/{customer}', [App\Http\Controllers\Api\CustomerController::class, 'show']);
        Route::post('/customers', [App\Http\Controllers\Api\CustomerController::class, 'store']);
        Route::put('/customers/{customer}', [App\Http\Controllers\Api\CustomerController::class, 'update']);

        // Visits
        Route::post('/visits', [App\Http\Controllers\Api\VisitController::class, 'store']);
        Route::get('/customers/{customer}/visits', [App\Http\Controllers\Api\VisitController::class, 'customerVisits']);

        // Loyalty
        Route::get('/customers/{customer}/wallet', [App\Http\Controllers\Api\LoyaltyController::class, 'wallet']);
        Route::get('/customers/{customer}/ledger', [App\Http\Controllers\Api\LoyaltyController::class, 'ledger']);
        Route::post('/customers/{customer}/redeem', [App\Http\Controllers\Api\LoyaltyController::class, 'redeem']);

        // Dashboard stats
        Route::get('/dashboard/stats', [App\Http\Controllers\Api\DashboardController::class, 'stats']);
    });
});

// Fallback route
Route::fallback(function () {
    return view('errors.404');
});

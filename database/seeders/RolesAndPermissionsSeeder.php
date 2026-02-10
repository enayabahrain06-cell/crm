<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions grouped by module
        $permissions = [
            // Dashboard
            'dashboards.view',

            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.export',

            // Visits
            'visits.view',
            'visits.create',
            'visits.edit',
            'visits.delete',

            // Loyalty Wallets
            'loyalty_wallets.view',

            // Loyalty Rules
            'loyalty_rules.view',
            'loyalty_rules.create',
            'loyalty_rules.edit',
            'loyalty_rules.delete',
            'loyalty_rules.settings',

            // Rewards
            'rewards.view',
            'rewards.create',
            'rewards.edit',
            'rewards.delete',
            'rewards.redeem',

            // Campaigns
            'campaigns.view',
            'campaigns.create',
            'campaigns.edit',
            'campaigns.delete',
            'campaigns.send',

            // Auto Greetings
            'auto_greetings.view',
            'auto_greetings.create',
            'auto_greetings.edit',
            'auto_greetings.delete',
            'auto_greetings.manage',

            // Outlets
            'outlets.view',
            'outlets.create',
            'outlets.edit',
            'outlets.delete',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Reports
            'reports.view',
            'reports.export',

            // Import/Export
            'import_export.view',
            'import_export.import',
            'import_export.export',

            // Settings
            'settings.view',
            'settings.edit',

            // Audit Logs
            'audit_logs.view',

            // Backups
            'backups.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                ],
                [
                    'module' => explode('.', $permission)[0],
                ]
            );
        }

        // Create roles and assign permissions
        $roles = [
            'super_admin' => $permissions, // All permissions

            'group_manager' => [
                'dashboard.view',
                'customers.view', 'customers.create', 'customers.edit', 'customers.export',
                'visits.view', 'visits.create', 'visits.edit',
                'loyalty_wallets.view',
                'loyalty_rules.view', 'loyalty_rules.create', 'loyalty_rules.edit', 'loyalty_rules.delete', 'loyalty_rules.settings',
                'rewards.view', 'rewards.create', 'rewards.edit', 'rewards.redeem',
                'campaigns.view', 'campaigns.create', 'campaigns.edit', 'campaigns.send',
                'auto_greetings.view', 'auto_greetings.create', 'auto_greetings.edit', 'auto_greetings.manage',
                'outlets.view',
                'reports.view', 'reports.export',
                'import_export.view', 'import_export.import', 'import_export.export',
                'audit_logs.view',
            ],

            'marketing_officer' => [
                'dashboard.view',
                'customers.view', 'customers.export',
                'loyalty_wallets.view',
                'loyalty_rules.view',
                'campaigns.view', 'campaigns.create', 'campaigns.edit', 'campaigns.send',
                'auto_greetings.view', 'auto_greetings.create', 'auto_greetings.edit', 'auto_greetings.manage',
                'reports.view', 'reports.export',
            ],

            'outlet_manager' => [
                'dashboard.view',
                'customers.view', 'customers.create', 'customers.edit',
                'visits.view', 'visits.create', 'visits.edit',
                'loyalty_wallets.view',
                'loyalty_rules.view', 'loyalty_rules.create', 'loyalty_rules.edit',
                'rewards.view', 'rewards.redeem',
                'outlets.view', 'outlets.edit',
                'users.view',
                'reports.view',
            ],

            'outlet_staff' => [
                'dashboard.view',
                'customers.view',
                'visits.view', 'visits.create',
                'loyalty_wallets.view',
                'loyalty_rules.view',
                'rewards.view',
                'users.view',
            ],

            'analytics_readonly' => [
                'dashboard.view',
                'customers.view',
                'visits.view',
                'loyalty_wallets.view',
                'loyalty_rules.view',
                'rewards.view',
                'campaigns.view',
                'reports.view',
                'outlets.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(
                [
                    'name' => $roleName,
                    'guard_name' => 'web',
                ],
                [
                    'description' => $this->getRoleDescription($roleName),
                ]
            );

            $permissions = Permission::whereIn('name', $rolePermissions)->get();
            $role->syncPermissions($permissions);
        }
    }

    protected function getRoleDescription(string $roleName): string
    {
        $descriptions = [
            'super_admin' => 'Full system access - can manage all resources and settings',
            'group_manager' => 'Manages all outlets and customers across the group',
            'marketing_officer' => 'Manages campaigns and customer communications',
            'outlet_manager' => 'Manages a specific outlet and its staff',
            'outlet_staff' => 'Basic staff access for day-to-day operations',
            'analytics_readonly' => 'Read-only access to reports and dashboards',
        ];

        return $descriptions[$roleName] ?? '';
    }
}


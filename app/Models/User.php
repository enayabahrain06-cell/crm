<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Roles that can access the admin section
     */
    protected static $adminSectionRoles = [
        'super_admin',
        'group_manager',
    ];

    /**
     * Outlet relationship (many-to-many through outlet_user)
     */
    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_user')
            ->withPivot(['role_at_outlet'])
            ->withTimestamps();
    }

    /**
     * Get the outlet IDs assigned to this user
     */
    public function getAssignedOutletIdsAttribute()
    {
        return $this->outlets()->pluck('outlets.id')->toArray();
    }

    /**
     * Check if user has access to a specific outlet
     */
    public function hasOutletAccess($outletId): bool
    {
        // Super admins have access to all outlets
        if ($this->hasRole('super_admin')) {
            return true;
        }
        
        return in_array($outletId, $this->assigned_outlet_ids);
    }

    /**
     * Check if user can access all outlets
     */
    public function canAccessAllOutlets(): bool
    {
        return $this->hasRole(['super_admin', 'group_manager', 'marketing_officer', 'analytics_readonly']);
    }

    /**
     * Check if user can access the admin section
     */
    public function canAccessAdminSection(): bool
    {
        return $this->hasRole(static::$adminSectionRoles);
    }

    /**
     * Check if user can access outlets
     */
    public function canAccessOutlets(): bool
    {
        return $this->hasRole(['super_admin', 'group_manager', 'manager', 'outlet_manager', 'outlet_staff']);
    }

    /**
     * Set the roles that can access the admin section
     */
    public static function setAdminSectionRoles(array $roles): void
    {
        static::$adminSectionRoles = $roles;
    }

    /**
     * Get the roles that can access the admin section
     */
    public static function getAdminSectionRoles(): array
    {
        return static::$adminSectionRoles;
    }

    /**
     * Scope for users by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->role($role);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get all permissions with module grouping
     */
    public function getPermissionsByModuleAttribute(): array
    {
        $permissions = $this->getAllPermissions();
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $module = $permission->module ?? 'general';
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $permission->name;
        }
        
        return $grouped;
    }
}


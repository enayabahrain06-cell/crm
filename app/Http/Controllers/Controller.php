<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Custom permission map for controllers that use non-standard permission names.
     * Format: 'ability' => 'actual_permission_name'
     * Example: ['view' => 'loyalty_rules.view', 'create' => 'rewards.create']
     */
    protected $customPermissionMap = [];

    /**
     * Check if user has permission for an action
     */
    protected function authorize($ability, $arguments = [])
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Check for custom permission first (for non-standard permissions like loyalty_rules.view)
        $customPermission = $this->getCustomPermission($ability);
        if ($customPermission && $user && $this->hasPermission($user, $customPermission)) {
            return true;
        }

        // Check by permission name (for standard permissions)
        $permission = $this->getPermissionName($ability);
        if ($permission && $user && $this->hasPermission($user, $permission)) {
            return true;
        }

        // Fall back to Laravel's authorize (only for resource-based permissions)
        try {
            $this->authorizeResource($user, $arguments);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, 'Unauthorized action. Permission not granted.');
        }

        return true;
    }

    /**
     * Check if user has a specific permission, handling non-existent permissions gracefully.
     */
    protected function hasPermission($user, string $permission): bool
    {
        if (!$user || !is_object($user) || !method_exists($user, 'hasPermissionTo')) {
            return false;
        }

        try {
            return $user->hasPermissionTo($permission);
        } catch (PermissionDoesNotExist $e) {
            // Permission doesn't exist in the database - treat as no permission
            return false;
        }
    }

    /**
     * Get custom permission from the controller's permission map
     */
    protected function getCustomPermission(string $ability): ?string
    {
        return $this->customPermissionMap[$ability] ?? null;
    }

    /**
     * Check if user has specific permission
     */
    protected function checkPermission($permission)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $this->hasPermission($user, $permission);
    }

    /**
     * Get permission name from ability
     */
    protected function getPermissionName(string $ability): string
    {
        $resource = str_replace('Controller', '', class_basename($this));

        // Convert to snake_case by inserting underscores before capital letters (except first)
        // This ensures 'AutoGreeting' becomes 'auto_greeting' not 'autogreeting'
        $resource = preg_replace('/(?<!^)[A-Z]/', '_$0', $resource);
        $resource = strtolower($resource);

        // Pluralize the resource name (e.g., auto_greeting → auto_greetings)
        $resource = \Illuminate\Support\Str::plural($resource);

        $abilityMap = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete',
        ];

        $action = $abilityMap[$ability] ?? $ability;

        return "{$resource}.{$action}";
    }

    /**
     * Authorize resource based on class name and action
     */
    protected function authorizeResource($user, $arguments = [])
    {
        // Skip authorization if user is not authenticated (for route:list command)
        if (!$user || !is_object($user) || !method_exists($user, 'hasPermissionTo')) {
            return true;
        }

        $resource = str_replace('Controller', '', class_basename($this));
        // Convert to snake_case by inserting underscores before capital letters (except first)
        // This ensures 'AutoGreeting' becomes 'auto_greeting' not 'autogreeting'
        $resource = preg_replace('/(?<!^)[A-Z]/', '_$0', $resource);
        $resource = strtolower($resource);
        $resource = \Illuminate\Support\Str::plural($resource);

        // Determine the action from the call stack
        $action = $this->getResourceAction();

        // Map actions to permissions
        $abilityMap = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete',
        ];

        $permissionAction = $abilityMap[$action] ?? $action;
        $permission = "{$resource}.{$permissionAction}";

        // Check if user has the required permission (safely, without throwing exception)
        if ($this->hasPermission($user, $permission)) {
            return true;
        }

        // This is a simplified authorization - in production, use policies
        abort(403, "Unauthorized. Permission '{$permission}' is required.");
    }

    /**
     * Determine the current resource action from the call stack
     */
    protected function getResourceAction(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

        foreach ($trace as $frame) {
            if (isset($frame['function'])) {
                $function = $frame['function'];
                // Common resource method names
                if (in_array($function, ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'])) {
                    return $function;
                }
            }
        }

        return 'view';
    }
}


<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\User;

class SidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $user = auth()->user();
        
        // Get roles that can access admin section
        $adminSectionRoles = User::getAdminSectionRoles();
        
        $view->with([
            'adminSectionRoles' => $adminSectionRoles,
            'userCanAccessAdmin' => $user ? $user->canAccessAdminSection() : false,
        ]);
    }
}


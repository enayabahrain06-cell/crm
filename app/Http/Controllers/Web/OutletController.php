<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOutletRequest;
use App\Http\Requests\UpdateOutletRequest;
use App\Models\Outlet;
use App\Models\OutletSocialLink;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OutletController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display outlets list
     */
    public function index(Request $request)
    {
        $this->authorize('outlets.view');

        $user = Auth::user();
        
        if ($user->hasRole('super_admin') || $user->hasRole('group_manager')) {
            $outlets = Outlet::withCount('users')->orderBy('name')->get();
        } else {
            $outlets = $user->outlets()->withCount('users')->get();
        }

        // Get outlet revenue by month data
        $selectedYear = (int) $request->get('year', now()->year);
        $outletRevenue = $this->dashboardService->getOutletRevenueByMonth($selectedYear);
        $availableYears = $this->dashboardService->getAvailableYears();

        return view('outlets.index', compact(
            'outlets',
            'outletRevenue',
            'selectedYear',
            'availableYears'
        ));
    }

    /**
     * Show outlet details
     */
    public function show(Outlet $outlet)
    {
        $this->authorize('outlets.view');
        $this->authorizeOutletAccess($outlet);

        $outlet->load(['socialLinks' => fn($q) => $q->active()->ordered()]);

        return view('outlets.show', compact('outlet'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('outlets.create');

        return view('outlets.create');
    }

    /**
     * Store new outlet
     */
    public function store(StoreOutletRequest $request)
    {
        $this->authorize('outlets.create');

        $data = $request->validated();
        
        // Handle active status - checkbox unchecked means false
        if (!isset($data['active'])) {
            $data['active'] = false;
        }
        
        $data['code'] = $this->generateUniqueCode($data['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('outlets/logos', 'public');
        }

        // Handle hero_image upload
        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $request->file('hero_image')->store('outlets/heroes', 'public');
        }

        $outlet = Outlet::create($data);

        return redirect()
            ->route('outlets.show', $outlet)
            ->with('success', 'Outlet created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit(Outlet $outlet)
    {
        $this->authorize('outlets.edit');
        $this->authorizeOutletAccess($outlet);

        return view('outlets.edit', compact('outlet'));
    }

    /**
     * Update outlet
     */
    public function update(UpdateOutletRequest $request, Outlet $outlet)
    {
        $this->authorize('outlets.edit');
        $this->authorizeOutletAccess($outlet);

        $data = $request->validated();

        // Handle active status - checkbox unchecked means false
        if (!isset($data['active'])) {
            $data['active'] = false;
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($outlet->logo && Storage::exists($outlet->logo)) {
                Storage::delete($outlet->logo);
            }
            $data['logo'] = $request->file('logo')->store('outlets/logos', 'public');
        }

        // Handle hero_image upload
        if ($request->hasFile('hero_image')) {
            // Delete old hero_image if exists
            if ($outlet->hero_image && Storage::exists($outlet->hero_image)) {
                Storage::delete($outlet->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('outlets/heroes', 'public');
        }

        $outlet->update($data);

        return redirect()
            ->route('outlets.show', $outlet)
            ->with('success', 'Outlet updated successfully.');
    }

    /**
     * Delete outlet
     */
    public function destroy(Outlet $outlet)
    {
        $this->authorize('outlets.delete');

        $outlet->delete();

        return redirect()
            ->route('outlets.index')
            ->with('success', 'Outlet deleted successfully.');
    }

    /**
     * Manage social links for outlet
     */
    public function socialLinks(Outlet $outlet)
    {
        $this->authorize('outlets.edit');
        $this->authorizeOutletAccess($outlet);

        $outlet->load(['socialLinks' => fn($q) => $q->ordered()]);

        return view('outlets.social-links', compact('outlet'));
    }

    /**
     * Store social link
     */
    public function storeSocialLink(Request $request, Outlet $outlet)
    {
        $this->authorize('outlets.edit');
        $this->authorizeOutletAccess($outlet);

        $data = $request->validate([
            'platform' => 'required|string',
            'label' => 'required|string|max:50',
            'url' => 'required|url',
            'color' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $outlet->socialLinks()->create($data);

        return back()->with('success', 'Social link added successfully.');
    }

    /**
     * Update social link
     */
    public function updateSocialLink(Request $request, Outlet $outlet, OutletSocialLink $socialLink)
    {
        $this->authorize('outlets.edit');
        $this->authorizeOutletAccess($outlet);

        $socialLink->update($request->validate([
            'platform' => 'required|string',
            'label' => 'required|string|max:50',
            'url' => 'required|url',
            'color' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]));

        return back()->with('success', 'Social link updated successfully.');
    }

    /**
     * Delete social link
     */
    public function destroySocialLink(Outlet $outlet, OutletSocialLink $socialLink)
    {
        $this->authorize('outlets.edit');
        $this->authorizeOutletAccess($outlet);

        $socialLink->delete();

        return back()->with('success', 'Social link deleted successfully.');
    }

    /**
     * Generate QR code URL
     */
    public function qrCode(Outlet $outlet)
    {
        $this->authorize('outlets.view');
        $this->authorizeOutletAccess($outlet);

        $registrationUrl = route('public.register', ['outlet' => $outlet->code]);

        return view('outlets.qr-code', compact('outlet', 'registrationUrl'));
    }

    /**
     * Get users for outlet
     */
    public function users(Outlet $outlet)
    {
        $this->authorize('outlets.view');
        $this->authorizeOutletAccess($outlet);

        $users = $outlet->users()->with('roles')->get();
        
        // Get all users that can be added to this outlet
        $availableUsers = \App\Models\User::whereDoesntHave('outlets', function ($query) use ($outlet) {
            $query->where('outlets.id', $outlet->id);
        })->orderBy('name')->get();

        return view('outlets.users', compact('outlet', 'users', 'availableUsers'));
    }

    /**
     * Store new user for outlet
     */
    public function storeUser(Request $request, Outlet $outlet)
    {
        $this->authorize('outlets.edit');
        $this->authorizeOutletAccess($outlet);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_at_outlet' => 'nullable|string|max:50',
        ]);

        // Check if user is already assigned
        if ($outlet->users()->where('users.id', $request->user_id)->exists()) {
            return back()->with('error', 'User is already assigned to this outlet.');
        }

        $outlet->users()->attach($request->user_id, [
            'role_at_outlet' => $request->role_at_outlet ?? 'staff',
        ]);

        return back()->with('success', 'User added to outlet successfully.');
    }

    /**
     * Remove user from outlet
     */
    public function destroyUser(Outlet $outlet, \App\Models\User $user)
    {
        $this->authorize('outlets.edit');
        $this->authorizeOutletAccess($outlet);

        $outlet->users()->detach($user->id);

        return back()->with('success', 'User removed from outlet successfully.');
    }

    /**
     * Generate unique outlet code
     */
    protected function generateUniqueCode(string $name): string
    {
        $code = str()->slug($name);
        $original = $code;
        $counter = 1;

        while (Outlet::where('code', $code)->exists()) {
            $code = $original . '-' . $counter++;
        }

        return $code;
    }

    /**
     * Authorize outlet access for current user
     */
    protected function authorizeOutletAccess(Outlet $outlet): void
    {
        $user = Auth::user();
        
        if (!$user->hasRole('super_admin') && !$user->hasRole('group_manager')) {
            if (!$user->outlets()->where('outlets.id', $outlet->id)->exists()) {
                abort(403);
            }
        }
    }
}


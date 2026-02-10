<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h4>
            <i class="bi bi-building" style="color: var(--primary-light);"></i>
            {{ setting('app_name', config('app.name', 'Hospitality CRM')) }}
        </h4>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Section Divider -->
            <li class="nav-divider"></li>

            <!-- Core Operations Section -->
            @if(auth()->user()->can('customers.view') || auth()->user()->can('visits.view'))
            <li class="nav-section">
                <div class="nav-section-header" data-bs-toggle="collapse" data-bs-target="#operationsCollapse" aria-expanded="true" aria-controls="operationsCollapse">
                    <span><i class="bi bi-gear-fill me-2"></i>Core Operations</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </div>
                <ul class="nav flex-column collapse show" id="operationsCollapse">
                    @can('customers.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('customers.*') && !request()->routeIs('customers.360') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                            <i class="bi bi-people"></i>
                            <span>Customers</span>
                            <span class="badge">{{ \App\Models\Customer::count() }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('visits.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('visits.*') ? 'active' : '' }}" href="{{ route('visits.index') }}">
                            <i class="bi bi-calendar-check"></i>
                            <span>Visits</span>
                            <span class="badge">{{ \App\Models\Visit::whereDate('created_at', today())->count() }}</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            <!-- Section Divider -->
            @if(auth()->user()->can('outlets.view') || auth()->user()->can('rewards.view') || auth()->user()->can('loyalty_wallets.view') || auth()->user()->can('loyalty_rules.view'))
            <li class="nav-divider"></li>

            <!-- Business Assets Section -->
            <li class="nav-section">
                <div class="nav-section-header" data-bs-toggle="collapse" data-bs-target="#assetsCollapse" aria-expanded="true" aria-controls="assetsCollapse">
                    <span><i class="bi bi-building-fill me-2"></i>Business Assets</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </div>
                <ul class="nav flex-column collapse show" id="assetsCollapse">
                    @can('outlets.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('outlets.index') && !request()->routeIs('outlets.qr') && !request()->routeIs('outlets.social-links') ? 'active' : '' }}" href="{{ route('outlets.index') }}">
                            <i class="bi bi-shop"></i>
                            <span>Outlets</span>
                            <span class="badge">{{ \App\Models\Outlet::count() }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('rewards.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('loyalty.rewards*') ? 'active' : '' }}" href="{{ route('loyalty.rewards') }}">
                            <i class="bi bi-gift"></i>
                            <span>Loyalty Rewards</span>
                        </a>
                    </li>
                    @endcan
                    @can('loyalty_wallets.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('loyalty.wallets*') ? 'active' : '' }}" href="{{ route('loyalty.wallets') }}">
                            <i class="bi bi-wallet2"></i>
                            <span>Loyalty Wallets</span>
                        </a>
                    </li>
                    @endcan
                    @can('loyalty_rules.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('loyalty.rules*') ? 'active' : '' }}" href="{{ route('loyalty.rules.index') }}">
                            <i class="bi bi-gear"></i>
                            <span>Loyalty Rules</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            <!-- Section Divider -->
            @if(auth()->user()->can('categories.view') || auth()->user()->can('products.view'))
            <li class="nav-divider"></li>

            <!-- Master Data Section -->
            <li class="nav-section">
                <div class="nav-section-header" data-bs-toggle="collapse" data-bs-target="#masterDataCollapse" aria-expanded="true" aria-controls="masterDataCollapse">
                    <span><i class="bi bi-database-fill me-2"></i>Master Data</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </div>
                <ul class="nav flex-column collapse show" id="masterDataCollapse">
                    @can('categories.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            <i class="bi bi-tags"></i>
                            <span>Categories</span>
                            <span class="badge">{{ \App\Models\Category::count() }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('products.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="bi bi-box-seam"></i>
                            <span>Products</span>
                            <span class="badge">{{ \App\Models\Product::count() }}</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            <!-- Section Divider -->
            @if(auth()->user()->can('campaigns.view') || auth()->user()->can('auto_greetings.view'))
            <li class="nav-divider"></li>

            <!-- Growth & Marketing Section -->
            <li class="nav-section">
                <div class="nav-section-header" data-bs-toggle="collapse" data-bs-target="#growthCollapse" aria-expanded="true" aria-controls="growthCollapse">
                    <span><i class="bi bi-graph-up-fill me-2"></i>Growth & Marketing</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </div>
                <ul class="nav flex-column collapse show" id="growthCollapse">
                    @can('campaigns.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('campaigns.*') ? 'active' : '' }}" href="{{ route('campaigns.index') }}">
                            <i class="bi bi-megaphone"></i>
                            <span>Campaigns</span>
                            <span class="badge">{{ \App\Models\Campaign::where('status', 'draft')->count() }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('auto_greetings.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('auto-greetings.*') ? 'active' : '' }}" href="{{ route('auto-greetings.index') }}">
                            <i class="bi bi-calendar-heart"></i>
                            <span>Auto Greetings</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            <!-- Section Divider -->
            @if(auth()->user()->can('reports.view') || auth()->user()->can('import_export.view'))
            <li class="nav-divider"></li>

            <!-- Insights & Analytics Section -->
            <li class="nav-section">
                <div class="nav-section-header" data-bs-toggle="collapse" data-bs-target="#insightsCollapse" aria-expanded="true" aria-controls="insightsCollapse">
                    <span><i class="bi bi-bar-chart-fill me-2"></i>Insights & Analytics</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </div>
                <ul class="nav flex-column collapse show" id="insightsCollapse">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                            <i class="bi bi-bar-chart"></i>
                            <span>Overview</span>
                        </a>
                    </li>
                    @can('reports.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('reports.customers') ? 'active' : '' }}" href="{{ route('reports.customers') }}">
                            <i class="bi bi-people-chart"></i>
                            <span>Customer Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('reports.visits') ? 'active' : '' }}" href="{{ route('reports.visits') }}">
                            <i class="bi bi-calendar-check"></i>
                            <span>Visit Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('reports.loyalty') ? 'active' : '' }}" href="{{ route('reports.loyalty') }}">
                            <i class="bi bi-gift"></i>
                            <span>Loyalty Report</span>
                        </a>
                    </li>
                    @endcan
                    @can('import_export.view')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('import-export.*') ? 'active' : '' }}" href="{{ route('import-export.index') }}">
                            <i class="bi bi-file-earmark-arrow"></i>
                            <span>Import/Export</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            <!-- Section Divider -->
            @if(auth()->user()->can('users.view') || auth()->user()->can('roles.view') || auth()->user()->can('settings.view') || auth()->user()->can('audit_logs.view') || auth()->user()->can('backups.view') || auth()->user()->hasRole(['super_admin', 'group_manager', 'outlet_manager', 'outlet_staff']))
            <li class="nav-divider"></li>

            <!-- Administration Section (Permission-based) -->
            @if(auth()->user()->hasRole(['super_admin', 'group_manager', 'outlet_manager', 'outlet_staff']))
            <li class="nav-section admin-section">
                <div class="nav-section-header admin-header" data-bs-toggle="collapse" data-bs-target="#adminCollapse" aria-expanded="false" aria-controls="adminCollapse">
                    <span><i class="bi bi-shield-fill me-2"></i>Administration</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </div>
                <ul class="nav flex-column collapse" id="adminCollapse">
                    @can('users.view')
                    <li class="nav-item">
                        <div class="admin-link-wrapper">
                            <a class="nav-link admin-link d-flex align-items-center {{ request()->routeIs('admin.users*') && !request()->routeIs('admin.settings*') && !request()->routeIs('admin.audit-logs*') && !request()->routeIs('admin.backup*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-person-badge"></i>
                                <span>Users & Roles</span>
                                <span class="badge">{{ \App\Models\User::count() }}</span>
                            </a>
                        </div>
                    </li>
                    @endcan
                    @can('settings.view')
                    <li class="nav-item">
                        <a class="nav-link admin-link d-flex align-items-center {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <i class="bi bi-sliders"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    @endcan
                    @can('audit_logs.view')
                    <li class="nav-item">
                        <a class="nav-link admin-link d-flex align-items-center {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}">
                            <i class="bi bi-journal-text"></i>
                            <span>Audit Logs</span>
                        </a>
                    </li>
                    @endcan
                    @can('backups.view')
                    <li class="nav-item">
                        <a class="nav-link admin-link d-flex align-items-center {{ request()->routeIs('admin.backup*') ? 'active' : '' }}" href="{{ route('admin.backup.index') }}">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <span>Backups</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif
            @endif
        </ul>
    </nav>
</aside>

<style>
/* Sidebar-specific styles are now in layouts/app.blade.php */
/* This file contains only the HTML structure */
</style>


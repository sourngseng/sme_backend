<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>

    <!-- Mobile Drawer Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebarCollapse()"></div>

    <!-- Super Admin Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Brand / Header -->
        <div class="sidebar-brand-box">
            <svg width="34" height="34" viewBox="0 0 64 64" fill="none" style="flex-shrink: 0;">
                <path d="M8 44C18 40.5 27 43 31 46.5V20.5C27 17 18 14.5 8 18V44Z" fill="#10B981"/>
                <path d="M56 44C46 40.5 37 43 33 46.5V20.5C37 17 46 14.5 56 18V44Z" fill="#F97316"/>
                <path d="M31 46.5C31.5 46.8 32.5 46.8 33 46.5V20.5C32.5 20.2 31.5 20.2 31 20.5V46.5Z" fill="#0284C7"/>
                <path d="M32 24V11" stroke="#22C55E" stroke-width="3" stroke-linecap="round"/>
                <path d="M32 17C26 17 25 12 28 10C31 10 32 14 32 17Z" fill="#22C55E"/>
                <path d="M32 15C38 15 39 9 36 8C33 8 32 12 32 15Z" fill="#16A34A"/>
            </svg>
            <div style="line-height: 1.2; overflow: hidden;">
                <div class="sidebar-brand-title">SchoolMeal</div>
                <div class="sidebar-brand-subtitle">{{ transLabel('form.hero_platform_tag', 'E-Commerce & Nutrition Platform') }}</div>
            </div>
            <!-- Mobile Drawer Close Button -->
            <button type="button" onclick="toggleSidebarCollapse()" class="sidebar-close-btn" style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer; display: none;">✕</button>
        </div>

        <!-- Scrollable Navigation Menu -->
        <div class="sidebar-menu">
            <!-- Primary Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
                </div>
            </a>

            <!-- Category: MANAGEMENT -->
            <div class="sidebar-section-title">{{ transLabel('menu.management', 'MANAGEMENT') }}</div>

            <!-- Users & Roles (collapsible, open by default) -->
            <div class="sidebar-dropdown-group open" id="groupUsers">
                <div class="sidebar-item" onclick="toggleSidebarDropdown('groupUsers')">
                    <div class="sidebar-item-left">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>{{ transLabel('menu.users_roles', 'Users & Roles') }}</span>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="sidebar-chevron"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
                <div class="sidebar-submenu">
                    <a href="javascript:void(0)" onclick="showToast('Viewing All Users roster', 'info')" class="submenu-item">{{ transLabel('menu.all_users', 'All Users') }}</a>
                    <a href="javascript:void(0)" onclick="showToast('Role permissions management', 'info')" class="submenu-item">{{ transLabel('menu.role_permissions', 'Role Permissions') }}</a>
                    <a href="javascript:void(0)" onclick="showToast('System Administrators list', 'info')" class="submenu-item">{{ transLabel('menu.system_administrators', 'System Administrators') }}</a>
                </div>
            </div>

            <!-- Schools -->
            <a href="{{ Route::has('school.dashboard') ? route('school.dashboard') : 'javascript:void(0)' }}" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/><circle cx="12" cy="9" r="2"/></svg>
                    <span>{{ transLabel('menu.schools', 'Schools') }}</span>
                </div>
            </a>

            <!-- Students -->
            <a href="{{ Route::has('student.dashboard') ? route('student.dashboard') : 'javascript:void(0)' }}" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M5.5 21v-2a6.5 6.5 0 0 1 13 0v2"/></svg>
                    <span>{{ transLabel('menu.students', 'Students') }}</span>
                </div>
            </a>

            <!-- Restaurants -->
            <a href="{{ Route::has('restaurant.dashboard') ? route('restaurant.dashboard') : 'javascript:void(0)' }}" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.85"/></svg>
                    <span>{{ transLabel('menu.restaurants', 'Restaurants') }}</span>
                </div>
            </a>

            <!-- Menu & Nutrition (collapsible) -->
            <div class="sidebar-dropdown-group" id="groupMenu">
                <div class="sidebar-item" onclick="toggleSidebarDropdown('groupMenu')">
                    <div class="sidebar-item-left">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v10"/><path d="M8 11h8"/></svg>
                        <span>{{ transLabel('menu.menu_nutrition', 'Menu & Nutrition') }}</span>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="sidebar-chevron"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
                <div class="sidebar-submenu">
                    <a href="javascript:void(0)" onclick="showToast('Dishes Catalog opened', 'info')" class="submenu-item">{{ transLabel('menu.catalog_dishes', 'Dishes Catalog') }}</a>
                    <a href="javascript:void(0)" onclick="showToast('Weekly Meal Cycles opened', 'info')" class="submenu-item">{{ transLabel('menu.weekly_menu', 'Weekly Meal Cycles') }}</a>
                    <a href="javascript:void(0)" onclick="showToast('Nutritional Guidelines opened', 'info')" class="submenu-item">{{ transLabel('menu.dietary_allergies', 'Nutritional Guidelines') }}</a>
                </div>
            </div>

            <!-- Orders (collapsible) -->
            <div class="sidebar-dropdown-group" id="groupOrders">
                <div class="sidebar-item" onclick="toggleSidebarDropdown('groupOrders')">
                    <div class="sidebar-item-left">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>{{ transLabel('menu.orders', 'Orders') }}</span>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="sidebar-chevron"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
                <div class="sidebar-submenu">
                    <a href="#listsTrack" onclick="scrollSection('listsTrack', event)" class="submenu-item">{{ transLabel('menu.active_orders', 'Active Orders') }}</a>
                    <a href="{{ Route::has('kitchen.dashboard') ? route('kitchen.dashboard') : 'javascript:void(0)' }}" class="submenu-item">{{ transLabel('menu.live_prep_queue', 'Live Kitchen Orders') }}</a>
                    <a href="#graphsTrack" onclick="scrollSection('graphsTrack', event)" class="submenu-item">{{ transLabel('menu.order_history', 'Order Analytics') }}</a>
                </div>
            </div>

            <!-- Payments -->
            <a href="{{ Route::has('finance.dashboard') ? route('finance.dashboard') : 'javascript:void(0)' }}" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    <span>{{ transLabel('menu.payments', 'Payments') }}</span>
                </div>
            </a>

            <!-- Delivery & Drivers (collapsible) -->
            <div class="sidebar-dropdown-group" id="groupDelivery">
                <div class="sidebar-item" onclick="toggleSidebarDropdown('groupDelivery')">
                    <div class="sidebar-item-left">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/><path d="M17 18h2a1 1 0 0 0 1-1v-3.5a1 1 0 0 0-.3-.7l-2.4-2.4a1 1 0 0 0-.7-.4H14"/></svg>
                        <span>{{ transLabel('menu.delivery_drivers', 'Delivery & Drivers') }}</span>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="sidebar-chevron"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
                <div class="sidebar-submenu">
                    <a href="{{ Route::has('driver.dashboard') ? route('driver.dashboard') : 'javascript:void(0)' }}" class="submenu-item">{{ transLabel('menu.drivers', 'Active Drivers') }}</a>
                    <a href="javascript:void(0)" onclick="showToast('Fleet Dispatch live routing', 'info')" class="submenu-item">{{ transLabel('menu.daily_deliveries', 'Daily Deliveries') }}</a>
                    <a href="javascript:void(0)" onclick="showToast('Live navigation map center', 'info')" class="submenu-item">{{ transLabel('menu.live_nav_map', 'Live Tracking Map') }}</a>
                </div>
            </div>

            <!-- Category: FINANCE -->
            <div class="sidebar-section-title">{{ transLabel('menu.finance', 'FINANCE') }}</div>

            <!-- Commissions -->
            <a href="javascript:void(0)" onclick="showToast('Commission Rules & Rates matrix', 'info')" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="M9 9h.01"/><path d="M15 15h.01"/></svg>
                    <span>{{ transLabel('menu.commissions', 'Commissions') }}</span>
                </div>
            </a>

            <!-- Settlements -->
            <a href="{{ Route::has('finance.dashboard') ? route('finance.dashboard') : 'javascript:void(0)' }}" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><line x1="9" y1="22" x2="9" y2="12"/><line x1="15" y1="22" x2="15" y2="12"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>{{ transLabel('menu.settlements', 'Settlements') }}</span>
                </div>
            </a>

            <!-- Financial Reports -->
            <a href="{{ Route::has('finance.dashboard') ? route('finance.dashboard') : 'javascript:void(0)' }}" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
                    <span>{{ transLabel('menu.financial_reports', 'Financial Reports') }}</span>
                </div>
            </a>

            <!-- Category: OPERATIONS -->
            <div class="sidebar-section-title">{{ transLabel('menu.operations', 'OPERATIONS') }}</div>

            <!-- Marketing (collapsible, open by default) -->
            <div class="sidebar-dropdown-group open" id="groupMarketing">
                <div class="sidebar-item" onclick="toggleSidebarDropdown('groupMarketing')">
                    <div class="sidebar-item-left">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                        <span>{{ transLabel('menu.marketing', 'Marketing') }}</span>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="sidebar-chevron"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
                <div class="sidebar-submenu">
                    <a href="{{ Route::has('marketing.dashboard') ? route('marketing.dashboard') : 'javascript:void(0)' }}" class="submenu-item">{{ transLabel('menu.campaign_analytics', 'Campaign Analytics') }}</a>
                    <a href="javascript:void(0)" onclick="showToast('Discount Vouchers & Promos', 'info')" class="submenu-item">{{ transLabel('menu.discount_vouchers', 'Discount Vouchers') }}</a>
                    <a href="javascript:void(0)" onclick="showToast('Seasonal Promotion campaigns', 'info')" class="submenu-item">{{ transLabel('menu.seasonal_promotions', 'Seasonal Promotions') }}</a>
                </div>
            </div>

            <!-- Notifications -->
            <a href="javascript:void(0)" onclick="toggleNotifDropdown(event)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    <span>{{ transLabel('menu.notifications', 'Notifications') }}</span>
                </div>
                <span class="sidebar-badge">5</span>
            </a>

            <!-- Support Tickets -->
            <a href="{{ Route::has('support.dashboard') ? route('support.dashboard') : 'javascript:void(0)' }}" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>{{ transLabel('menu.support_tickets', 'Support Tickets') }}</span>
                </div>
            </a>

            <!-- Category: REPORTS -->
            <div class="sidebar-section-title">{{ transLabel('menu.reports', 'REPORTS') }}</div>

            <!-- Analytics -->
            <a href="#graphsTrack" onclick="scrollSection('graphsTrack', event)" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <span>{{ transLabel('menu.analytics', 'Analytics') }}</span>
                </div>
            </a>

            <!-- Reports -->
            <a href="javascript:void(0)" onclick="showToast('Exporting consolidated reports', 'info')" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    <span>{{ transLabel('menu.reports_list', 'Reports') }}</span>
                </div>
            </a>

            <!-- Category: SYSTEM -->
            <div class="sidebar-section-title">{{ transLabel('menu.system', 'SYSTEM') }}</div>

            <!-- Profile & Security (Super Admin) -->
            <a href="{{ route('admin.profile') }}" class="sidebar-item {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>{{ transLabel('menu.profile_security', 'Profile & Security') }}</span>
                </div>
            </a>

            <!-- Manage Languages (Super Admin Dedicated) -->
            <a href="{{ route('admin.languages.index') }}" class="sidebar-item {{ request()->routeIs('admin.languages.*') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span>{{ transLabel('menu.manage_languages', 'Manage Languages') }}</span>
                </div>
            </a>

            <!-- System Settings (Super Admin Dedicated) -->
            <a href="{{ route('admin.system_settings.index') }}" class="sidebar-item {{ request()->routeIs('admin.system_settings.*') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>{{ transLabel('menu.system_settings', 'System Settings') }}</span>
                </div>
            </a>

            <!-- Audit Logs -->
            <a href="javascript:void(0)" onclick="showToast('Loading Security & Audit Trail', 'info')" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                    <span>{{ transLabel('menu.audit_logs', 'Audit Logs') }}</span>
                </div>
            </a>

            <!-- System Health -->
            <a href="javascript:void(0)" onclick="showToast('Cluster services & DB health: 99.98% OK', 'success')" class="sidebar-item">
                <div class="sidebar-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    <span>{{ transLabel('menu.system_health', 'System Health') }}</span>
                </div>
            </a>
        </div>

        <!-- Pinned Bottom User Card -->
        <a href="{{ route('admin.profile') }}" class="sidebar-user-box {{ request()->routeIs('admin.profile*') ? 'active' : '' }}" id="sidebarUserBox" style="text-decoration: none; color: inherit;">
            <div class="sidebar-user-content">
                <img src="{{ auth()->user()->profile_image_url ?? asset('images/avatar_seng_sourng.jpg') }}" alt="User Avatar" class="sidebar-user-avatar" onerror="this.src='https://ui-avatars.com/api/?name=SENG+Sourng&background=1D4ED8&color=fff'">
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->full_name ?? 'SENG Sourng' }}</div>
                    <div class="sidebar-user-role">{{ transLabel('form.role_super_admin', 'Super Admin') }}</div>
                </div>
            </div>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-user-chevron"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </aside>

    <!-- Main Workspace -->
    <div class="admin-main">
        <!-- Topbar with Improved Notification Dropdown -->
        @include('layouts.partials.topbar')

        <!-- Main Content View -->
        <main class="admin-content">
            @hasSection('header_title')
                <div style="margin-bottom: 1.25rem;">
                    <h1 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">@yield('header_title')</h1>
                </div>
            @endif
            @yield('content')
        </main>

        <!-- Super Admin Mobile Bottom Navigation Bar -->
        <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Mobile Navigation">
            <a href="{{ route('admin.dashboard') }}" class="mobile-nav-tab {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" id="mobileTabDashboard">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
            </a>

            <a href="#graphsTrack" class="mobile-nav-tab" onclick="scrollSection('graphsTrack', event)" id="mobileTabAnalytics">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('dashboard.analytics', 'Analytics') }}</span>
            </a>

            <a href="#listsTrack" class="mobile-nav-tab" onclick="scrollSection('listsTrack', event)" id="mobileTabOrders">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('dashboard.orders', 'Orders') }}</span>
            </a>

            <button type="button" class="mobile-nav-tab" onclick="toggleNotifDropdown(event)" id="mobileTabNotifs" aria-label="Notifications">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    <span class="mobile-tab-badge">5</span>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.alerts', 'Alerts') }}</span>
            </button>

            <button type="button" class="mobile-nav-tab" onclick="toggleSidebarCollapse()" id="mobileTabMenu" aria-label="Open Full Menu">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.menu', 'Menu') }}</span>
            </button>
        </nav>
    </div>

    <!-- Floating Global Toast Notifications Container -->
    <x-toast-container />

    <!-- Interactive Scripts -->
    @include('layouts.partials.scripts')
</body>
</html>

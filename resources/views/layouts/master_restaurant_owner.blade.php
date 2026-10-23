<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
    <style>
        /* Dedicated Kitchen / Restaurant Owner Sidebar Theme */
        .admin-sidebar {
            width: 250px;
            background: #0f172a;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            overflow: hidden;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.18);
        }

        .kitchen-sidebar-header {
            padding: 1.25rem 1rem 1.15rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.65rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            flex-shrink: 0;
            background: #0f172a;
        }

        .kitchen-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }

        .kitchen-avatar-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f59e0b;
            box-shadow: 0 2px 10px rgba(245, 158, 11, 0.35);
        }

        .kitchen-info {
            min-width: 0;
            flex: 1;
            line-height: 1.25;
        }

        .kitchen-title {
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .kitchen-role {
            color: #94a3b8;
            font-size: 0.725rem;
            font-weight: 500;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .kitchen-status-pill {
            background: #16a34a;
            color: #ffffff;
            font-size: 0.675rem;
            font-weight: 800;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            letter-spacing: 0.02em;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(22, 163, 74, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-menu {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.85rem 0.75rem 1.75rem 0.75rem;
            min-height: 0;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 4px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.68rem 0.85rem;
            color: #94a3b8;
            border-radius: 10px;
            font-size: 1.1rem !important;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
            margin-bottom: 0.25rem;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
        }

        .sidebar-item.active {
            background: #2563eb !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.38);
        }

        .sidebar-item-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .sidebar-item-left svg {
            flex-shrink: 0;
            color: currentColor;
            opacity: 0.9;
        }

        .sidebar-section-title {
            font-size: 0.6875rem;
            font-weight: 800;
            color: #64748b;
            letter-spacing: 0.08em;
            padding: 1.15rem 0.5rem 0.45rem 0.5rem;
            text-transform: uppercase;
        }

        .kitchen-badge-count {
            background: #ef4444;
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 0.15rem 0.55rem;
            border-radius: 999px;
            line-height: 1;
            letter-spacing: 0;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>
<body>

    <!-- Mobile Drawer Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebarCollapse()"></div>

    <!-- SchoolMeal Kitchen Operations Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Top Kitchen Profile Box (No redundant brand box, exactly as in reference design) -->
        <div class="kitchen-sidebar-header">
            <div class="kitchen-avatar-wrap">
                <img src="{{ asset('images/khmer_kitchen_avatar.jpg') }}" alt="Khmer Kitchen" class="kitchen-avatar-img" onerror="this.src='https://ui-avatars.com/api/?name=KK&background=EA580C&color=fff'">
            </div>
            <div class="kitchen-info">
                <div class="kitchen-title">Khmer Kitchen</div>
                <div class="kitchen-role">{{ transLabel('roles.restaurant_owner', 'Kitchen Admin') }}</div>
            </div>
            <span class="kitchen-status-pill">{{ transLabel('status.online', 'Online') }}</span>
        </div>

        <!-- Navigation Menu with Independent Scrolling -->
        <div class="sidebar-menu">
            <!-- 1. Primary Dashboard (Blue Pill) -->
            <a href="{{ Route::has('restaurant.dashboard') ? route('restaurant.dashboard') : (Route::has('kitchen.dashboard') ? route('kitchen.dashboard') : route('admin.dashboard')) }}" class="sidebar-item {{ request()->routeIs('restaurant.dashboard*') || request()->routeIs('kitchen.dashboard*') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
                </div>
            </a>

            <!-- Category: KITCHEN OPERATIONS -->
            <div class="sidebar-section-title">{{ transLabel('menu.kitchen_ops_sec', 'KITCHEN OPERATIONS') }}</div>

            <!-- Kitchen Queue (12) -->
            <a href="#kitchenQueueSection" class="sidebar-item" onclick="if(document.getElementById('kitchenQueueSection')) { document.getElementById('kitchenQueueSection').scrollIntoView({behavior: 'smooth'}); }">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <span>{{ transLabel('menu.kitchen_queue', 'Kitchen Queue') }}</span>
                </div>
                <span class="kitchen-badge-count">12</span>
            </a>

            <!-- Preparing Meals -->
            <a href="#preparingNowSection" class="sidebar-item" onclick="if(document.getElementById('preparingNowSection')) { document.getElementById('preparingNowSection').scrollIntoView({behavior: 'smooth'}); }">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v2"/><path d="M7 3v2"/><path d="M17 3v2"/><path d="M4 11h16a1 1 0 0 1 1 1v2a6 6 0 0 1-6 6h-6a6 6 0 0 1-6-6v-2a1 1 0 0 1 1-1Z"/><path d="M6 11V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v4"/></svg>
                    <span>{{ transLabel('menu.preparing_meals', 'Preparing Meals') }}</span>
                </div>
            </a>

            <!-- Completed Meals -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="window.Toast ? window.Toast.info('Viewing completed meals archive') : null;">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ transLabel('menu.completed_meals', 'Completed Meals') }}</span>
                </div>
            </a>

            <!-- Meal Schedule -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="window.Toast ? window.Toast.info('Viewing weekly meal preparation schedule') : null;">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>{{ transLabel('menu.meal_schedule', 'Meal Schedule') }}</span>
                </div>
            </a>

            <!-- Menu Items -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="window.Toast ? window.Toast.info('Managing kitchen recipe and menu catalog') : null;">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/><path d="M6 14h6"/></svg>
                    <span>{{ transLabel('menu.menu_items', 'Menu Items') }}</span>
                </div>
            </a>

            <!-- Category: ORDERS -->
            <div class="sidebar-section-title">{{ transLabel('menu.orders', 'ORDERS') }}</div>

            <!-- Incoming Orders (8) -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="window.Toast ? window.Toast.info('8 new incoming orders pending review') : null;">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    <span>{{ transLabel('menu.incoming_orders', 'Incoming Orders') }}</span>
                </div>
                <span class="kitchen-badge-count">8</span>
            </a>

            <!-- Order History -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="window.Toast ? window.Toast.info('Accessing kitchen order archives') : null;">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/></svg>
                    <span>{{ transLabel('menu.order_history', 'Order History') }}</span>
                </div>
            </a>

            <!-- Cancellations -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="window.Toast ? window.Toast.info('No pending cancellation requests') : null;">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span>{{ transLabel('menu.cancellations', 'Cancellations') }}</span>
                </div>
            </a>

            <!-- Category: DELIVERY -->
            <div class="sidebar-section-title">{{ transLabel('menu.delivery', 'DELIVERY') }}</div>

            <!-- Delivery Handover -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="window.Toast ? window.Toast.info('Delivery Handover queue ready for driver check-out') : null;">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                    <span>{{ transLabel('menu.delivery_handover', 'Delivery Handover') }}</span>
                </div>
            </a>

            <!-- Drivers -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="window.Toast ? window.Toast.info('4 delivery drivers currently assigned to route') : null;">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                    <span>{{ transLabel('menu.drivers', 'Drivers') }}</span>
                </div>
            </a>

            <!-- Category: REPORTS -->
            <div class="sidebar-section-title">{{ transLabel('menu.reports', 'REPORTS') }}</div>

            <!-- Daily Summary -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="window.Toast ? window.Toast.info('Daily summary generated for kitchen shift') : null;">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                    <span>{{ transLabel('menu.daily_summary', 'Daily Summary') }}</span>
                </div>
            </a>

            <!-- Category: ACCOUNT -->
            <div class="sidebar-section-title">{{ transLabel('menu.account', 'ACCOUNT') }}</div>

            <!-- Profile -->
            <a href="{{ route('restaurant.profile') }}" class="sidebar-item {{ request()->routeIs('restaurant.profile*') ? 'active' : '' }}">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>{{ transLabel('menu.profile', 'Profile') }}</span>
                </div>
            </a>

            <!-- Notifications (3) -->
            <a href="javascript:void(0)" class="sidebar-item" onclick="toggleNotifDropdown(event)">
                <div class="sidebar-item-left">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    <span>{{ transLabel('menu.notifications', 'Notifications') }}</span>
                </div>
                <span class="kitchen-badge-count">3</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="sidebar-item" style="width: 100%; background: none; border: none; text-align: left; cursor: pointer; color: #ef4444 !important; font-family: inherit;">
                    <div class="sidebar-item-left">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span>{{ transLabel('menu.sign_out', 'Logout') }}</span>
                    </div>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Workspace -->
    <div class="admin-main">
        <!-- Topbar with Improved Notification Dropdown & Merchant Avatar -->
        @include('layouts.partials.topbar', [
            'customUserName' => auth()->user()?->full_name ?? 'Khmer Kitchen Admin',
            'customUserRole' => transLabel('form.role_restaurant_owner', 'Restaurant Owner'),
            'customUserAvatar' => auth()->user()?->profile_image_url ?? asset('images/khmer_kitchen_avatar.jpg')
        ])

        <!-- Main Content View -->
        <main class="admin-content" style="background: #f8fafc;">
            @yield('content')
        </main>

        <!-- Kitchen Admin Mobile Bottom Navigation Bar -->
        <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Mobile Navigation">
            <a href="{{ Route::has('restaurant.dashboard') ? route('restaurant.dashboard') : (Route::has('kitchen.dashboard') ? route('kitchen.dashboard') : route('admin.dashboard')) }}" class="mobile-nav-tab active">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
            </a>

            <a href="#kitchenQueueSection" class="mobile-nav-tab" onclick="if(document.getElementById('kitchenQueueSection')) { document.getElementById('kitchenQueueSection').scrollIntoView({behavior: 'smooth'}); }">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <span class="mobile-tab-badge">12</span>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.queue', 'Queue') }}</span>
            </a>

            <a href="#preparingNowSection" class="mobile-nav-tab" onclick="if(document.getElementById('preparingNowSection')) { document.getElementById('preparingNowSection').scrollIntoView({behavior: 'smooth'}); }">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v2"/><path d="M7 3v2"/><path d="M17 3v2"/><path d="M4 11h16a1 1 0 0 1 1 1v2a6 6 0 0 1-6 6h-6a6 6 0 0 1-6-6v-2a1 1 0 0 1 1-1Z"/><path d="M6 11V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v4"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.prep', 'Prep') }}</span>
            </a>

            <button type="button" class="mobile-nav-tab" onclick="toggleNotifDropdown(event)" id="mobileTabNotifs">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    <span class="mobile-tab-badge">3</span>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.alerts', 'Alerts') }}</span>
            </button>

            <button type="button" class="mobile-nav-tab" onclick="toggleSidebarCollapse()" id="mobileTabMenu">
                <div class="mobile-tab-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                </div>
                <span class="mobile-tab-label">{{ transLabel('menu.menu', 'Menu') }}</span>
            </button>
        </nav>
    </div>

    <!-- Floating Global Toast Notifications Container (No inline banners per AGENTS.md) -->
    <x-toast-container />

    <!-- Interactive Scripts -->
    @include('layouts.partials.scripts')
</body>
</html>

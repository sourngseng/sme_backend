<header class="admin-topbar">
    <div class="topbar-left">
        <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebarCollapse()" aria-label="Toggle Navigation Drawer">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>

        <div class="search-container">
            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="{{ transLabel('form.search_placeholder', 'Search schools, restaurants, orders, users...') }}" aria-label="Global Search">
        </div>
    </div>

    <!-- Right: Alerts, Language & User Profile Dropdown -->
    <div class="topbar-actions">

        <!-- 1. Improved Notification Center with Dropdown -->
        <div style="position: relative;">
            <button type="button" class="topbar-icon-btn" id="notifBtn" onclick="toggleNotifDropdown(event)" title="{{ transLabel('menu.notifications', 'Notifications') }}" aria-label="Notifications">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                <span class="badge-dot">5</span>
            </button>

            <!-- Improved Floating & Mobile Non-Clipping Notification Dropdown -->
            <div id="notifDropdown" class="dropdown-panel notification-dropdown">
                <div class="notif-header">
                    <div class="notif-header-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        <span>{{ transLabel('menu.notifications', 'Notifications') }}</span>
                    </div>
                    <span class="notif-unread-badge">5 {{ transLabel('common.new', 'New') }}</span>
                </div>

                <div class="notif-list-container">
                    <!-- Item 1: Restaurant Registered -->
                    <div class="notif-item unread">
                        <div class="notif-icon-box" style="background: #eff6ff; color: #1D4ED8;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title">New Restaurant Registered</div>
                            <div class="notif-desc">Healthy Food Co. submitted documents for review.</div>
                            <div class="notif-time">2 mins ago</div>
                        </div>
                        <div class="notif-unread-dot"></div>
                    </div>

                    <!-- Item 2: School Approved -->
                    <div class="notif-item unread">
                        <div class="notif-icon-box" style="background: #ecfdf5; color: #059669;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/></svg>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title">New School Approved</div>
                            <div class="notif-desc">Wat Bo Primary School onboarding setup complete.</div>
                            <div class="notif-time">12 mins ago</div>
                        </div>
                        <div class="notif-unread-dot"></div>
                    </div>

                    <!-- Item 3: Payment Received -->
                    <div class="notif-item unread">
                        <div class="notif-icon-box" style="background: #faf5ff; color: #9333ea;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title">Payment Received (KHQR)</div>
                            <div class="notif-desc">ORD-00121 - $3.50 settled via Bakong KHQR.</div>
                            <div class="notif-time">18 mins ago</div>
                        </div>
                        <div class="notif-unread-dot"></div>
                    </div>

                    <!-- Item 4: Delivery Dispatched -->
                    <div class="notif-item unread">
                        <div class="notif-icon-box" style="background: #fffbeb; color: #d97706;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title">Delivery Batch Dispatched</div>
                            <div class="notif-desc">Route #4 is out for delivery to Angkor Primary.</div>
                            <div class="notif-time">25 mins ago</div>
                        </div>
                        <div class="notif-unread-dot"></div>
                    </div>

                    <!-- Item 5: Nutrition Audit -->
                    <div class="notif-item">
                        <div class="notif-icon-box" style="background: #f1f5f9; color: #475569;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title">Nutrition Safety Audit</div>
                            <div class="notif-desc">Daily calorie and allergen checks passed 100%.</div>
                            <div class="notif-time">42 mins ago</div>
                        </div>
                    </div>
                </div>

                <div class="notif-footer">
                    <a href="javascript:void(0)">
                        <span>{{ transLabel('common.view_all', 'View All Notifications') }}</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Help Center -->
        <button type="button" class="topbar-icon-btn help-center-btn" title="{{ transLabel('common.help_center', 'Help Center') }}" onclick="window.Toast ? window.Toast.info('SchoolMeal Support Desk & Knowledge Base is available 24/7.') : alert('SchoolMeal Support Desk')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </button>

        <!-- 3. Language Selector Dropdown -->
        <div style="position: relative;">
            @php
                $activeLocale = app()->getLocale();
            @endphp
            <button type="button" class="topbar-icon-btn lang-toggle-btn" id="langToggleBtn" onclick="toggleLangDropdown(event)" title="{{ $activeLocale === 'km' ? 'ភាសាខ្មែរ' : 'English' }}" aria-label="Select Language">
                <img src="{{ $activeLocale === 'km' ? asset('images/flags/km.svg') : asset('images/flags/gb.svg') }}" 
                     alt="{{ $activeLocale === 'km' ? 'Cambodia Flag' : 'UK Flag' }}" 
                     class="lang-flag-current" 
                     width="22" 
                     height="15">
                <span id="currentLangText" class="lang-text-desktop">{{ $activeLocale === 'km' ? 'ភាសាខ្មែរ' : 'English' }}</span>
                <svg class="lang-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <!-- Language Switcher Popup -->
            <div id="langDropdownMenu" class="dropdown-panel" style="width: 185px; padding: 0.35rem;">
                <form method="POST" action="{{ route('locale.switch') }}" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="locale" value="en">
                    <button type="submit" style="width: 100%; border: none; background: transparent; padding: 0.55rem 0.75rem; font-size: 0.8125rem; font-weight: 600; color: {{ $activeLocale === 'en' ? '#1D4ED8' : '#1e293b' }}; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; text-align: left; font-family: inherit;">
                        <span style="display: flex; align-items: center; gap: 0.6rem;">
                            <img src="{{ asset('images/flags/gb.svg') }}" alt="UK Flag" style="width: 20px; height: 14px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.08);">
                            <span>English</span>
                        </span>
                        @if($activeLocale === 'en') <span style="color: #2563EB; font-weight: 800;">✓</span> @endif
                    </button>
                </form>
                <form method="POST" action="{{ route('locale.switch') }}" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="locale" value="km">
                    <button type="submit" style="width: 100%; border: none; background: transparent; padding: 0.55rem 0.75rem; font-size: 0.8125rem; font-weight: 600; color: {{ $activeLocale === 'km' ? '#1D4ED8' : '#1e293b' }}; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; text-align: left; font-family: inherit;">
                        <span style="display: flex; align-items: center; gap: 0.6rem;">
                            <img src="{{ asset('images/flags/km.svg') }}" alt="Cambodia Flag" style="width: 20px; height: 14px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.08);">
                            <span style="font-family: 'Hanuman', serif; font-size: 0.875rem;">ភាសាខ្មែរ</span>
                        </span>
                        @if($activeLocale === 'km') <span style="color: #2563EB; font-weight: 800;">✓</span> @endif
                    </button>
                </form>
                @if(Route::has('admin.languages.index'))
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('admin.languages.index') }}" style="display: flex; align-items: center; gap: 0.45rem; padding: 0.45rem 0.75rem; font-size: 0.75rem; font-weight: 700; color: #1D4ED8; text-decoration: none; border-radius: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <span>{{ transLabel('menu.manage_languages', 'Manage Languages') }}</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- 4. User Profile Dropdown Button -->
        @php
            $authUser = auth()->user();
            $userName = $authUser?->full_name ?? ($customUserName ?? 'SENG Sourng');
            $userEmail = $authUser?->email ?? 'staff@khmerkitchen.com';
            $userAvatar = $authUser?->profile_image_url ?? ($customUserAvatar ?? asset('images/avatar_sourng.jpg'));

            $isSuperAdmin = $authUser && $authUser->hasRole(['super_admin', 'admin']);
            $isKitchen = $authUser && $authUser->hasRole(['restaurant_staff', 'kitchen_staff']);
            $isRestaurant = $authUser && $authUser->hasRole('restaurant_owner');

            $userRole = $customUserRole ?? ($authUser && $authUser->roles->isNotEmpty()
                ? ucwords(str_replace('_', ' ', $authUser->roles->first()->name))
                : 'Staff');

            if ($isSuperAdmin) {
                $profileUrl = route('admin.profile');
                $settingsUrl = route('admin.profile.settings');
                $securityUrl = route('admin.profile.security');
            } elseif ($isKitchen) {
                $profileUrl = route('kitchen.profile');
                $settingsUrl = route('kitchen.profile.settings');
                $securityUrl = route('kitchen.profile.security');
            } elseif ($isRestaurant) {
                $profileUrl = route('restaurant.profile');
                $settingsUrl = route('restaurant.profile.settings');
                $securityUrl = route('restaurant.profile.security');
            } else {
                $profileUrl = Route::has('admin.profile') ? route('admin.profile') : (Route::has('kitchen.profile') ? route('kitchen.profile') : 'javascript:void(0)');
                $settingsUrl = Route::has('admin.profile.settings') ? route('admin.profile.settings') : (Route::has('kitchen.profile.settings') ? route('kitchen.profile.settings') : 'javascript:void(0)');
                $securityUrl = Route::has('admin.profile.security') ? route('admin.profile.security') : (Route::has('kitchen.profile.security') ? route('kitchen.profile.security') : 'javascript:void(0)');
            }
        @endphp
        <div style="position: relative;">
            <button type="button" class="user-profile-btn" id="userProfileBtn" onclick="toggleProfileDropdown(event)">
                <img src="{{ $userAvatar }}" alt="User Avatar" class="topbar-avatar" onerror="this.src='https://ui-avatars.com/api/?name=User&background=1D4ED8&color=fff'">
                <!-- <div class="user-profile-meta" style="text-align: left; line-height: 1.2;">
                    <div style="font-size: 0.8125rem; font-weight: 700; color: #0f172a;">{{ $userName }}</div>
                    <div style="font-size: 0.6875rem; color: #64748b;">{{ $userRole }}</div>
                </div> -->
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <!-- Floating Profile Menu -->
            <div id="userProfileDropdown" class="dropdown-panel profile-dropdown">
                <div class="profile-dropdown-header">
                    <img src="{{ $userAvatar }}" alt="User" class="profile-dropdown-avatar" onerror="this.src='https://ui-avatars.com/api/?name=User&background=1D4ED8&color=fff'">
                    <div style="overflow: hidden;">
                        <div style="font-weight: 800; font-size: 0.875rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $userName }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $userEmail }}</div>
                        <div style="display: inline-block; margin-top: 4px; padding: 0.15rem 0.5rem; background: #eff6ff; color: #1D4ED8; font-size: 0.675rem; font-weight: 700; border-radius: 999px;">
                            {{ $userRole }}
                        </div>
                    </div>
                </div>

                <div class="profile-dropdown-body">
                    <a href="{{ $profileUrl }}" class="profile-menu-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>{{ transLabel('menu.my_profile', 'My Profile') }}</span>
                    </a>
                    <a href="{{ $settingsUrl }}" class="profile-menu-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span>{{ transLabel('menu.account_settings', 'Account Settings') }}</span>
                    </a>
                    <a href="{{ $securityUrl }}" class="profile-menu-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>{{ transLabel('menu.security_logs', 'Security & Sessions') }}</span>
                    </a>
                    @if($isSuperAdmin && Route::has('admin.system_settings.index'))
                    <a href="{{ route('admin.system_settings.index') }}" class="profile-menu-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span>{{ transLabel('menu.system_settings', 'System Settings') }}</span>
                    </a>
                    @endif

                    <div class="dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="profile-menu-link logout-link" style="width: 100%; border: none; background: transparent; cursor: pointer; font-family: inherit; text-align: left;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span>{{ transLabel('menu.sign_out', 'Sign Out') }}</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</header>

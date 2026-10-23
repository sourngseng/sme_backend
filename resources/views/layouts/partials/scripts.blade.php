<script>
    // 1. Toggle Sidebar Collapse (Full <-> Mini mode on desktop, or mobile drawer)
    function toggleSidebarCollapse() {
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (window.innerWidth <= 1024) {
            const isOpen = sidebar.classList.toggle('open');
            if (backdrop) {
                if (isOpen) {
                    backdrop.classList.add('active');
                    document.body.style.overflow = 'hidden';
                } else {
                    backdrop.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        } else {
            sidebar.classList.toggle('collapsed');
        }
    }

    // 2. Toggle Sidebar Dropdown Accordions
    function toggleSidebarDropdown(groupId) {
        const group = document.getElementById(groupId);
        if (group) {
            group.classList.toggle('open');
        }
    }

    // 3. Toggle Notification Dropdown (Improved Floating & Mobile Safe)
    function toggleNotifDropdown(event) {
        if (event) event.stopPropagation();
        const notifDropdown = document.getElementById('notifDropdown');
        const langDropdown = document.getElementById('langDropdownMenu');
        const profileDropdown = document.getElementById('userProfileDropdown');

        if (langDropdown) langDropdown.classList.remove('show');
        if (profileDropdown) profileDropdown.classList.remove('show');

        if (notifDropdown) {
            notifDropdown.classList.toggle('show');
        }
    }

    // 4. Toggle Language Switcher Popup
    function toggleLangDropdown(event) {
        if (event) event.stopPropagation();
        const langDropdown = document.getElementById('langDropdownMenu');
        const notifDropdown = document.getElementById('notifDropdown');
        const profileDropdown = document.getElementById('userProfileDropdown');

        if (notifDropdown) notifDropdown.classList.remove('show');
        if (profileDropdown) profileDropdown.classList.remove('show');

        if (langDropdown) {
            langDropdown.classList.toggle('show');
        }
    }

    // 5. Toggle Profile Dropdown Menu
    function toggleProfileDropdown(event) {
        if (event) event.stopPropagation();
        const profileDropdown = document.getElementById('userProfileDropdown');
        const notifDropdown = document.getElementById('notifDropdown');
        const langDropdown = document.getElementById('langDropdownMenu');

        if (notifDropdown) notifDropdown.classList.remove('show');
        if (langDropdown) langDropdown.classList.remove('show');

        if (profileDropdown) {
            profileDropdown.classList.toggle('show');
        }
    }

    // 6. Smooth Scroll Helper for Mobile Bottom Bar Navigation Tabs
    function scrollSection(targetId, event) {
        if (event) event.preventDefault();
        const targetEl = document.getElementById(targetId);
        if (targetEl) {
            targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            document.querySelectorAll('.mobile-nav-tab').forEach(t => t.classList.remove('active'));
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('active');
            }
        }
    }

    // 7. Global Window Listener to Close Dropdown Panels When Clicking Outside
    window.addEventListener('click', function(e) {
        const notifDropdown = document.getElementById('notifDropdown');
        const notifBtn = document.getElementById('notifBtn');
        const mobileTabNotifs = document.getElementById('mobileTabNotifs');
        if (notifDropdown && notifDropdown.classList.contains('show')) {
            if (!notifDropdown.contains(e.target) && (!notifBtn || !notifBtn.contains(e.target)) && (!mobileTabNotifs || !mobileTabNotifs.contains(e.target))) {
                notifDropdown.classList.remove('show');
            }
        }

        const langDropdown = document.getElementById('langDropdownMenu');
        const langToggleBtn = document.getElementById('langToggleBtn');
        if (langDropdown && langDropdown.classList.contains('show')) {
            if (!langDropdown.contains(e.target) && (!langToggleBtn || !langToggleBtn.contains(e.target))) {
                langDropdown.classList.remove('show');
            }
        }

        const profileDropdown = document.getElementById('userProfileDropdown');
        const userProfileBtn = document.getElementById('userProfileBtn');
        if (profileDropdown && profileDropdown.classList.contains('show')) {
            if (!profileDropdown.contains(e.target) && (!userProfileBtn || !userProfileBtn.contains(e.target))) {
                profileDropdown.classList.remove('show');
            }
        }
    });

    // Close mobile drawer when pressing Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                if (backdrop) backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
            const notifDropdown = document.getElementById('notifDropdown');
            if (notifDropdown) notifDropdown.classList.remove('show');
            const langDropdown = document.getElementById('langDropdownMenu');
            if (langDropdown) langDropdown.classList.remove('show');
            const profileDropdown = document.getElementById('userProfileDropdown');
            if (profileDropdown) profileDropdown.classList.remove('show');
        }
    });
</script>

@stack('scripts')

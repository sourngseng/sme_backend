<script>
    /**
     * Internationalization dictionary for Profile Client Interactions
     */
    const profileI18n = {
        secLevel: {!! json_encode(transLabel('profile.sec_level', 'Password security level')) !!},
        weakPassword: {!! json_encode(transLabel('profile.weak_password', 'Weak password')) !!},
        moderateSecurity: {!! json_encode(transLabel('profile.moderate_security', 'Moderate security')) !!},
        strongSecure: {!! json_encode(transLabel('profile.strong_secure', 'Strong & secure')) !!},
        photoLoaded: {!! json_encode(transLabel('profile.photo_loaded', 'Photo loaded for preview. Click "Save Changes" to apply.')) !!},
        presetSelected: {!! json_encode(transLabel('profile.preset_selected', 'Preset avatar selected. Click "Save Changes" to apply.')) !!},
        copiedToast: {!! json_encode(transLabel('profile.copied_toast', ':label copied to clipboard!')) !!}
    };

    /**
     * Switch Profile Tab & Update URL Query / State
     */
    function switchProfileTab(tabName, event) {
        if (event) {
            event.preventDefault();
        }

        // Hide all tabs
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.profile-tab-btn').forEach(el => el.classList.remove('active'));

        // Show active tab
        const targetTab = document.getElementById(tabName + 'TabPane');
        const targetBtn = document.getElementById(tabName + 'TabBtn');

        if (targetTab) targetTab.classList.add('active');
        if (targetBtn) targetBtn.classList.add('active');

        // Update URL history state seamlessly
        if (window.history && window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url.toString());
        }
    }

    /**
     * Toggle Password Input Visibility
     */
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
        } else {
            input.type = 'password';
            btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
        }
    }

    /**
     * Live Password Strength Evaluator
     */
    function evaluatePasswordStrength(password) {
        const fill = document.getElementById('strengthFill');
        const text = document.getElementById('strengthText');
        if (!fill || !text) return;

        let score = 0;
        const checks = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            numbers: /[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password),
        };

        // Update UI checklist
        updateRuleCheck('ruleLength', checks.length);
        updateRuleCheck('ruleUpper', checks.uppercase);
        updateRuleCheck('ruleLower', checks.lowercase);
        updateRuleCheck('ruleNumber', checks.numbers);

        if (checks.length) score += 25;
        if (checks.uppercase) score += 25;
        if (checks.lowercase) score += 25;
        if (checks.numbers) score += 25;

        fill.style.width = score + '%';

        if (score === 0) {
            fill.style.background = '#e2e8f0';
            text.textContent = profileI18n.secLevel;
            text.style.color = '#64748b';
        } else if (score <= 50) {
            fill.style.background = '#ef4444';
            text.textContent = profileI18n.weakPassword;
            text.style.color = '#ef4444';
        } else if (score <= 75) {
            fill.style.background = '#f59e0b';
            text.textContent = profileI18n.moderateSecurity;
            text.style.color = '#f59e0b';
        } else {
            fill.style.background = '#10b981';
            text.textContent = profileI18n.strongSecure;
            text.style.color = '#10b981';
        }
    }

    function updateRuleCheck(elementId, isValid) {
        const el = document.getElementById(elementId);
        if (!el) return;
        if (isValid) {
            el.classList.add('valid');
        } else {
            el.classList.remove('valid');
        }
    }

    /**
     * Live Avatar File Preview
     */
    function previewAvatarUpload(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('avatarPreviewTarget');
                const heroImg = document.getElementById('heroAvatarTarget');
                if (previewImg) previewImg.src = e.target.result;
                if (heroImg) heroImg.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);

            // Clear preset active chips
            document.querySelectorAll('.preset-chip').forEach(c => c.classList.remove('active'));
            const presetInput = document.getElementById('presetAvatarInput');
            if (presetInput) presetInput.value = '';

            if (window.Toast) {
                window.Toast.info(profileI18n.photoLoaded);
            }
        }
    }

    /**
     * Select a preset avatar
     */
    function selectPresetAvatar(avatarPath, chipEl) {
        document.querySelectorAll('.preset-chip').forEach(c => c.classList.remove('active'));
        if (chipEl) chipEl.classList.add('active');

        const presetInput = document.getElementById('presetAvatarInput');
        const fileInput = document.getElementById('avatarFileInput');
        const previewImg = document.getElementById('avatarPreviewTarget');
        const heroImg = document.getElementById('heroAvatarTarget');

        if (presetInput) presetInput.value = avatarPath;
        if (fileInput) fileInput.value = ''; // Clear file input
        if (previewImg) previewImg.src = avatarPath;
        if (heroImg) heroImg.src = avatarPath;

        if (window.Toast) {
            window.Toast.info(profileI18n.presetSelected);
        }
    }

    /**
     * Copy text to clipboard with Toast notification
     */
    function copyProfileText(text, label) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                if (window.Toast) {
                    const msg = profileI18n.copiedToast.replace(':label', label);
                    window.Toast.success(msg);
                }
            });
        }
    }

    // Auto-initialize active tab from query parameter on initial load if present
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam && ['overview', 'settings', 'security'].includes(tabParam)) {
            switchProfileTab(tabParam);
        }
    });
</script>

<!-- Global Toast Notification Container -->
<div id="toastContainer" class="toast-container" aria-live="polite" aria-atomic="true"></div>

<style>
    /* Fixed Toast Container */
    .toast-container {
        position: fixed;
        top: 1.25rem;
        right: 1.25rem;
        z-index: 999999;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        pointer-events: none;
        width: min(420px, calc(100vw - 2.5rem));
    }

    /* Base Toast Item */
    .toast-item {
        pointer-events: auto;
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        background: #ffffff;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transition: transform 0.2s ease, opacity 0.2s ease, max-height 0.3s ease, margin 0.3s ease, padding 0.3s ease;
        font-family: inherit;
    }

    .toast-item.toast-hiding {
        animation: toastSlideOut 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Variant Accents */
    .toast-item.toast-success {
        border-left: 4px solid #10B981;
    }
    .toast-item.toast-error {
        border-left: 4px solid #EF4444;
    }
    .toast-item.toast-warning {
        border-left: 4px solid #F59E0B;
    }
    .toast-item.toast-info {
        border-left: 4px solid #2563EB;
    }

    /* Icon Box */
    .toast-icon-wrap {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 0.05rem;
    }
    .toast-success .toast-icon-wrap {
        background: #ecfdf5;
        color: #059669;
    }
    .toast-error .toast-icon-wrap {
        background: #fef2f2;
        color: #DC2626;
    }
    .toast-warning .toast-icon-wrap {
        background: #fffbeb;
        color: #D97706;
    }
    .toast-info .toast-icon-wrap {
        background: #eff6ff;
        color: #2563EB;
    }

    /* Content */
    .toast-content {
        flex: 1;
        min-width: 0;
    }
    .toast-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 0.15rem;
    }
    .toast-message {
        font-size: 0.8rem;
        color: #475569;
        line-height: 1.4;
        word-break: break-word;
    }

    /* Close Button */
    .toast-close-btn {
        flex-shrink: 0;
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 0.2rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s, background-color 0.15s;
        margin-left: 0.25rem;
    }
    .toast-close-btn:hover {
        color: #0f172a;
        background: #f1f5f9;
    }

    /* Countdown Progress Bar */
    .toast-progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
        transform-origin: left;
        animation: toastProgress linear forwards;
    }
    .toast-success .toast-progress-bar {
        background: #10B981;
    }
    .toast-error .toast-progress-bar {
        background: #EF4444;
    }
    .toast-warning .toast-progress-bar {
        background: #F59E0B;
    }
    .toast-info .toast-progress-bar {
        background: #2563EB;
    }

    /* Animations */
    @keyframes toastSlideIn {
        from {
            opacity: 0;
            transform: translateX(110%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes toastSlideOut {
        from {
            opacity: 1;
            transform: translateX(0);
            max-height: 120px;
            margin-bottom: 0;
        }
        to {
            opacity: 0;
            transform: translateX(110%);
            max-height: 0;
            margin-bottom: -0.65rem;
            padding-top: 0;
            padding-bottom: 0;
        }
    }

    @keyframes toastProgress {
        from {
            transform: scaleX(1);
        }
        to {
            transform: scaleX(0);
        }
    }
</style>

<script>
    (function() {
        const ICONS = {
            success: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`,
            error: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
            warning: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
            info: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`
        };

        const TITLES = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Notice'
        };

        window.Toast = {
            show: function(type, message, title = null, duration = null) {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const validTypes = ['success', 'error', 'warning', 'info'];
                type = validTypes.includes(type) ? type : 'info';

                if (duration === null) {
                    duration = type === 'error' ? 6000 : 4500;
                }

                const toast = document.createElement('div');
                toast.className = `toast-item toast-${type}`;
                toast.setAttribute('role', 'alert');

                const displayTitle = title || TITLES[type];

                toast.innerHTML = `
                    <div class="toast-icon-wrap">${ICONS[type]}</div>
                    <div class="toast-content">
                        ${displayTitle ? `<div class="toast-title">${displayTitle}</div>` : ''}
                        <div class="toast-message">${message}</div>
                    </div>
                    <button type="button" class="toast-close-btn" aria-label="Close notification">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    ${duration > 0 ? `<div class="toast-progress-bar" style="animation-duration: ${duration}ms;"></div>` : ''}
                `;

                container.appendChild(toast);

                let hideTimeout = null;
                const dismiss = () => {
                    if (toast.classList.contains('toast-hiding')) return;
                    toast.classList.add('toast-hiding');
                    setTimeout(() => {
                        if (toast.parentElement) toast.parentElement.removeChild(toast);
                    }, 300);
                };

                if (duration > 0) {
                    hideTimeout = setTimeout(dismiss, duration);

                    // Pause timer on hover
                    toast.addEventListener('mouseenter', () => {
                        clearTimeout(hideTimeout);
                        const progress = toast.querySelector('.toast-progress-bar');
                        if (progress) progress.style.animationPlayState = 'paused';
                    });

                    toast.addEventListener('mouseleave', () => {
                        const progress = toast.querySelector('.toast-progress-bar');
                        if (progress) progress.style.animationPlayState = 'running';
                        hideTimeout = setTimeout(dismiss, 2000);
                    });
                }

                toast.querySelector('.toast-close-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    clearTimeout(hideTimeout);
                    dismiss();
                });

                return toast;
            },

            success: function(message, title = null, duration = null) {
                return this.show('success', message, title, duration);
            },

            error: function(message, title = null, duration = null) {
                return this.show('error', message, title, duration);
            },

            warning: function(message, title = null, duration = null) {
                return this.show('warning', message, title, duration);
            },

            info: function(message, title = null, duration = null) {
                return this.show('info', message, title, duration);
            }
        };

        // Universal backwards-compatible function
        window.showToast = function(message, type = 'success', duration = null) {
            window.Toast.show(type, message, null, duration);
        };
    })();
</script>

<!-- Server-side Session Flash Handler (Converts Laravel Session Status & Errors to Toasts) -->
@if(session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.Toast.success(@json(session('status')));
        });
    </script>
@endif

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.Toast.success(@json(session('success')));
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.Toast.error(@json(session('error')));
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.Toast.warning(@json(session('warning')));
        });
    </script>
@endif

@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.Toast.info(@json(session('info')));
        });
    </script>
@endif

@if(isset($errors) && $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($errors->all() as $errorMsg)
                window.Toast.error(@json($errorMsg));
            @endforeach
        });
    </script>
@endif

<style>
    /* ==========================================================================
       SCHOOL MEAL PROFILE, SETTINGS & SECURITY DESIGN SYSTEM
       ========================================================================== */
    .profile-page-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0 0 2.5rem 0;
    }

    @media (max-width: 768px) {
        .profile-page-wrapper {
            padding: 0 0 1rem 0;
        }
    }

    /* 1. Profile Hero Card */
    .profile-hero {
        position: relative;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f2744 100%);
        border-radius: 20px;
        padding: 2.25rem 2rem 1.75rem 2rem;
        color: #ffffff;
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        overflow: hidden;
        margin-bottom: 1.75rem;
    }

    .profile-hero-pattern {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1.5px, transparent 1.5px);
        background-size: 24px 24px;
        pointer-events: none;
        opacity: 0.6;
    }

    .profile-hero-glow {
        position: absolute;
        top: -40px;
        right: -40px;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.35) 0%, rgba(37, 99, 235, 0) 70%);
        pointer-events: none;
        border-radius: 50%;
    }

    .profile-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.75rem;
        flex-wrap: wrap;
    }

    .profile-hero-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex: 1;
        min-width: 280px;
    }

    .profile-avatar-container {
        position: relative;
        flex-shrink: 0;
    }

    .profile-hero-avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        object-fit: cover;
        border: 3.5px solid rgba(255, 255, 255, 0.95);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35), 0 0 0 3px rgba(37, 99, 235, 0.25);
        background: #1e293b;
        display: block;
    }

    .profile-avatar-badge {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 26px;
        height: 26px;
        background: #10b981;
        border: 2.5px solid #0f172a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 11px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .profile-meta-header {
        line-height: 1.35;
    }

    .profile-meta-title-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 0.35rem;
    }

    .profile-meta-name {
        font-size: 1.55rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.02em;
        margin: 0;
    }

    .profile-badges-row {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
    }

    .profile-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.75rem;
        background: rgba(37, 99, 235, 0.25);
        color: #93c5fd;
        border: 1px solid rgba(147, 197, 253, 0.35);
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .profile-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.65rem;
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, 0.3);
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .status-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseStatus 2s infinite;
    }

    @keyframes pulseStatus {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .profile-meta-subtitle {
        color: #cbd5e1;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.4;
    }

    .profile-meta-sub {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        color: #94a3b8;
        font-size: 0.8125rem;
        flex-wrap: wrap;
        margin-top: 0.65rem;
    }

    .profile-meta-sub-item {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
        font-size: 0.785rem;
        font-weight: 500;
        transition: all 0.15s ease;
    }

    .profile-meta-sub-item:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .profile-hero-quick-stats {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        background: rgba(255, 255, 255, 0.07);
        backdrop-filter: blur(12px);
        padding: 0.85rem 1.25rem;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .hero-stat-box {
        text-align: center;
        padding: 0 0.5rem;
    }

    .hero-stat-box:not(:last-child) {
        border-right: 1px solid rgba(255, 255, 255, 0.12);
        padding-right: 1.25rem;
    }

    .hero-stat-val {
        font-size: 1.25rem;
        font-weight: 800;
        color: #ffffff;
    }

    .hero-stat-lbl {
        font-size: 0.7rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 600;
        margin-top: 2px;
    }

    /* 2. Modern Navigation Tabs */
    .profile-tabs-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #ffffff;
        padding: 0.5rem;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.75rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .profile-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.75rem 1.25rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 700;
        color: #64748b;
        text-decoration: none;
        border: none;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        font-family: inherit;
    }

    .profile-tab-btn:hover {
        color: #1e293b;
        background: #f8fafc;
    }

    .profile-tab-btn.active {
        background: #1D4ED8;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(29, 78, 216, 0.3);
    }

    .profile-tab-btn.active svg {
        stroke: #ffffff;
    }

    .profile-tab-badge {
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 800;
        background: #f1f5f9;
        color: #475569;
    }

    .profile-tab-btn.active .profile-tab-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Tab Panes */
    .tab-pane {
        display: none;
        animation: fadeInTab 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .tab-pane.active {
        display: block;
    }

    @keyframes fadeInTab {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 3. Cards & Grid System */
    .profile-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .profile-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }

    .profile-grid-sidebar {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .profile-grid-sidebar {
            grid-template-columns: 1fr;
        }
        .profile-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .profile-grid-3 {
            grid-template-columns: 1fr;
        }
    }

    .settings-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .settings-card-header {
        padding: 1.25rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .card-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-header-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #1D4ED8;
        flex-shrink: 0;
    }

    .card-header-title {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .card-header-desc {
        font-size: 0.785rem;
        color: #64748b;
        margin-top: 2px;
    }

    .settings-card-body {
        padding: 1.5rem;
    }

    .settings-card-footer {
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* 4. Form Controls */
    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    @media (max-width: 640px) {
        .form-row-2 {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.45rem;
    }

    .form-label .required {
        color: #ef4444;
        margin-left: 2px;
    }

    .form-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .form-input-icon {
        position: absolute;
        left: 0.85rem;
        color: #94a3b8;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-control {
        width: 100%;
        height: 44px;
        padding: 0.65rem 0.95rem;
        padding-left: 2.65rem;
        font-size: 0.875rem;
        font-family: inherit;
        color: #0f172a;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        transition: all 0.15s ease-in-out;
    }

    .form-control.no-icon {
        padding-left: 0.95rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #2563EB;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    textarea.form-control {
        height: auto;
        min-height: 90px;
        padding-left: 0.95rem;
        resize: vertical;
    }

    .form-help {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.35rem;
    }

    /* 5. Custom Avatar Picker & File Upload */
    .avatar-picker-section {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.25rem;
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 14px;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .avatar-preview-box {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .avatar-preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-picker-content {
        flex: 1;
        min-width: 220px;
    }

    .preset-avatar-chips {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-top: 0.65rem;
        flex-wrap: wrap;
    }

    .preset-chip {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem 0.65rem;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 999px;
        cursor: pointer;
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        transition: all 0.15s ease;
    }

    .preset-chip:hover {
        border-color: #2563EB;
        color: #1D4ED8;
    }

    .preset-chip.active {
        border-color: #2563EB;
        background: #eff6ff;
        color: #1D4ED8;
    }

    .preset-chip img {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* 6. Buttons */
    .btn-brand-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%);
        color: #ffffff;
        border: none;
        padding: 0.65rem 1.35rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
    }

    .btn-brand-primary:hover {
        background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
    }

    .btn-brand-primary:active {
        transform: translateY(0);
    }

    .btn-brand-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #ffffff;
        color: #334155;
        border: 1.5px solid #cbd5e1;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    .btn-brand-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    .btn-brand-danger {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 0.55rem 1rem;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    .btn-brand-danger:hover {
        background: #fca5a5;
        color: #b91c1c;
    }

    /* 7. Password Strength Indicator */
    .password-strength-container {
        margin-top: 0.65rem;
    }

    .strength-meter-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 0.45rem;
    }

    .strength-meter-fill {
        height: 100%;
        width: 0%;
        transition: width 0.3s ease, background 0.3s ease;
        border-radius: 999px;
    }

    .password-rules-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.4rem;
        margin-top: 0.5rem;
    }

    .rule-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
    }

    .rule-item.valid {
        color: #10b981;
    }

    .rule-item.valid svg {
        stroke: #10b981;
    }

    /* 8. Active Sessions List */
    .session-item-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.15rem 1.25rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 0.85rem;
        transition: all 0.15s ease;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .session-item-card.current {
        border-color: #93c5fd;
        background: #f8fbff;
        box-shadow: 0 2px 10px rgba(37, 99, 235, 0.06);
    }

    .session-info-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .session-device-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #f1f5f9;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .session-item-card.current .session-device-icon-box {
        background: #eff6ff;
        color: #1D4ED8;
    }

    .session-meta-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
    }

    .session-meta-sub {
        font-size: 0.775rem;
        color: #64748b;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .current-session-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.6rem;
        background: #dcfce7;
        color: #15803d;
        border-radius: 999px;
        font-size: 0.725rem;
        font-weight: 800;
    }

    /* 9. Preference Toggles */
    .pref-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
        gap: 1rem;
    }

    .pref-item-row:last-child {
        border-bottom: none;
    }

    .pref-label-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #1e293b;
    }

    .pref-label-desc {
        font-size: 0.775rem;
        color: #64748b;
        margin-top: 2px;
    }

    /* Switch toggle */
    .switch-toggle {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
        flex-shrink: 0;
    }

    .switch-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 24px;
    }

    .switch-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    .switch-toggle input:checked + .switch-slider {
        background-color: #2563EB;
    }

    .switch-toggle input:checked + .switch-slider:before {
        transform: translateX(22px);
    }

    /* 10. Info Grid Details (My Profile Tab) */
    .info-list-group {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 0.95rem;
        background: #f8fafc;
        border-radius: 10px;
        font-size: 0.84rem;
    }

    .info-label {
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .info-value {
        font-weight: 700;
        color: #0f172a;
        text-align: right;
    }

    .info-chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 0.5rem;
    }

    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        background: #eff6ff;
        color: #1D4ED8;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* ==========================================================================
       11. RESPONSIVE DESIGN & MOBILE VIEWPORT POLISH
       ========================================================================== */
    @media (max-width: 900px) {
        .profile-hero-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.5rem;
        }

        .profile-hero-left {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1rem;
            width: 100%;
        }

        .profile-meta-header {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .profile-meta-title-row {
            justify-content: center;
            flex-direction: column;
            gap: 0.5rem;
        }

        .profile-badges-row {
            justify-content: center;
        }

        .profile-meta-sub {
            justify-content: center;
        }

        .profile-hero-quick-stats {
            width: 100%;
            max-width: 480px;
            justify-content: space-around;
        }
    }

    @media (max-width: 768px) {
        .profile-page-wrapper {
            padding: 0.85rem 0.75rem 0 0.75rem;
        }

        .profile-hero {
            padding: 1.65rem 1.15rem 1.35rem 1.15rem;
            border-radius: 18px;
            margin-bottom: 1.25rem;
        }

        .profile-hero-glow {
            width: 200px;
            height: 200px;
            top: -20px;
            right: -20px;
        }

        .profile-hero-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.15rem;
        }

        .profile-hero-left {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.95rem;
            width: 100%;
            min-width: 0;
        }

        .profile-avatar-container {
            margin: 0 auto;
        }

        .profile-hero-avatar {
            width: 88px;
            height: 88px;
            border-width: 3px;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2), 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .profile-avatar-badge {
            width: 24px;
            height: 24px;
            bottom: 0px;
            right: 0px;
        }

        .profile-meta-header {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .profile-meta-title-row {
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.35rem;
            width: 100%;
        }

        .profile-meta-name {
            font-size: 1.35rem;
            font-weight: 800;
            text-align: center;
            line-height: 1.25;
            width: 100%;
            word-break: break-word;
        }

        .profile-badges-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .profile-meta-subtitle {
            text-align: center;
            font-size: 0.825rem;
            color: #cbd5e1;
            margin-top: 0.2rem;
            line-height: 1.35;
        }

        .profile-meta-sub {
            justify-content: center;
            gap: 0.45rem;
            margin-top: 0.75rem;
            width: 100%;
        }

        .profile-meta-sub-item {
            font-size: 0.75rem;
            padding: 0.25rem 0.65rem;
            gap: 0.35rem;
            max-width: 100%;
        }

        .profile-meta-sub-item svg {
            flex-shrink: 0;
        }

        /* 3-column stats bar on mobile */
        .profile-hero-quick-stats {
            width: 100%;
            max-width: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.25rem;
            padding: 0.85rem 0.45rem;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            margin-top: 0.25rem;
        }

        .hero-stat-box {
            padding: 0 0.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .hero-stat-box:not(:last-child) {
            border-right: 1px solid rgba(255, 255, 255, 0.12);
            padding-right: 0.25rem;
        }

        .hero-stat-val {
            font-size: 1.2rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .hero-stat-lbl {
            font-size: 0.68rem;
            line-height: 1.2;
            margin-top: 3px;
            text-align: center;
            white-space: normal;
            word-break: break-word;
        }

        /* Tabs Nav on Mobile */
        .profile-tabs-nav {
            padding: 0.35rem;
            border-radius: 12px;
            gap: 0.35rem;
            margin-bottom: 1.25rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .profile-tabs-nav::-webkit-scrollbar {
            display: none;
        }

        .profile-tab-btn {
            flex: 1 1 0;
            justify-content: center;
            padding: 0.65rem 0.6rem;
            font-size: 0.8rem;
            gap: 0.4rem;
            min-width: 105px;
        }

        .profile-tab-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Cards & Headers on Mobile */
        .settings-card {
            border-radius: 14px;
            margin-bottom: 1.15rem;
        }

        .settings-card:last-child {
            margin-bottom: 0 !important;
        }

        .settings-card-header {
            padding: 1rem 1.15rem;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.65rem;
        }

        .card-header-icon {
            width: 34px;
            height: 34px;
        }

        .card-header-title {
            font-size: 0.95rem;
        }

        .settings-card-body {
            padding: 1.15rem;
        }

        .settings-card-footer {
            padding: 0.85rem 1.15rem;
            flex-direction: column-reverse;
            gap: 0.5rem;
        }

        .settings-card-footer button {
            width: 100%;
            justify-content: center;
        }

        /* Info Item on Mobile */
        .info-item {
            padding: 0.7rem 0.85rem;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .info-value {
            text-align: left;
            word-break: break-word;
        }

        /* Avatar Picker Section on Mobile */
        .avatar-picker-section {
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1.15rem;
            gap: 1rem;
        }

        .avatar-picker-content {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .preset-avatar-chips {
            justify-content: center;
        }

        /* Session Item Cards on Mobile */
        .session-item-card {
            padding: 0.85rem 0.95rem;
            gap: 0.75rem;
        }

        .session-info-left {
            gap: 0.75rem;
        }

        .session-device-icon-box {
            width: 38px;
            height: 38px;
        }

        .session-meta-name {
            font-size: 0.85rem;
        }

        .session-meta-sub {
            font-size: 0.72rem;
            gap: 0.4rem;
        }

        .pref-item-row {
            padding: 0.85rem 0;
            gap: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .profile-hero {
            padding: 1.45rem 0.95rem 1.25rem 0.95rem;
        }

        .profile-hero-avatar {
            width: 82px;
            height: 82px;
        }

        .profile-meta-name {
            font-size: 1.25rem;
        }

        .profile-meta-sub-item {
            font-size: 0.72rem;
            padding: 0.22rem 0.55rem;
        }

        .hero-stat-val {
            font-size: 1.1rem;
        }

        .hero-stat-lbl {
            font-size: 0.65rem;
        }
    }
</style>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>SchoolMeal - {{ transLabel('form.sign_in', 'Sign in to your account') }}</title>

    <!-- Google Fonts: Plus Jakarta Sans & Kantumruy Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@100;300;400;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            --font-khmer: 'Hanuman', 'Plus Jakarta Sans', serif, sans-serif;
            --brand-blue: #1D4ED8;
            --brand-blue-hover: #1E40AF;
            --brand-blue-light: #eff6ff;
            --brand-emerald: #10B981;
            --brand-orange: #EA580C;
            --brand-purple: #9333EA;
            --page-bg: #f1f5f9;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-radius: 16px;
        }

        /* ================= KHMER FONT ("HANUMAN") & SCALE ENHANCEMENT ================= */
        html[lang="km"],
        :lang(km) {
            font-family: var(--font-khmer);
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }

        html[lang="km"] .login-title {
            font-size: 1.65rem !important;
            font-weight: 900 !important;
            line-height: 1.35 !important;
        }

        html[lang="km"] .login-subtitle {
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
        }

        html[lang="km"] .form-label {
            font-size: 0.875rem !important;
            font-weight: 700 !important;
        }

        html[lang="km"] .form-control {
            font-size: 0.925rem !important;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .btn-login-submit {
            font-size: 0.975rem !important;
            font-weight: 700 !important;
            font-family: var(--font-khmer) !important;
        }

        html[lang="km"] .login-pill-badge,
        html[lang="km"] .tab-item {
            font-size: 0.825rem !important;
            font-weight: 700 !important;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Clean Normal Light Page - Strict 100vh Zero Scroll */
        html, body {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
            font-family: var(--font-sans);
            background-color: var(--page-bg);
            background-image: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            padding: 1rem;
        }

        /* Clean Normal Card Container - Fits screen without scroll */
        .login-card {
            width: 100%;
            max-width: 960px;
            height: min(580px, calc(100vh - 2rem));
            background: #ffffff;
            border-radius: var(--card-radius);
            border: 1px solid var(--border-color);
            box-shadow:
                0 20px 40px -15px rgba(15, 23, 42, 0.08),
                0 0 0 1px rgba(15, 23, 42, 0.02),
                0 4px 12px -2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            display: grid;
            grid-template-columns: 42% 58%;
            position: relative;
        }

        /* Left Hero Banner - Space-Saving & Clean */
        .hero-banner {
            position: relative;
            background-color: #081a38;
            background-image: url('/images/school_lunch_kids.jpg');
            background-size: cover;
            background-position: center 20%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 2.25rem 1.75rem 2.25rem;
            color: #ffffff;
            height: 100%;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(7, 26, 56, 0.96) 0%,
                rgba(9, 36, 76, 0.90) 38%,
                rgba(12, 34, 68, 0.60) 65%,
                rgba(8, 20, 40, 0.88) 100%
            );
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
        }

        .hero-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .hero-brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .hero-brand-tag {
            font-size: 0.7rem;
            color: #7dd3fc;
            font-weight: 500;
            letter-spacing: 0.02em;
        }

        .hero-headline {
            font-size: 1.85rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: -0.025em;
            margin-top: 1rem;
        }

        .hero-subtext {
            font-size: 0.8rem;
            color: rgba(226, 232, 240, 0.9);
            line-height: 1.45;
            margin-top: 0.5rem;
            margin-bottom: 1.25rem;
        }

        /* Compact Feature Pills */
        .feature-pills {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .feature-pill {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0.45rem 0.75rem;
            border-radius: 10px;
        }

        .feature-pill-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-pill-title {
            font-size: 0.785rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .feature-pill-desc {
            font-size: 0.675rem;
            color: rgba(226, 232, 240, 0.8);
            line-height: 1.2;
        }

        .hero-motto {
            font-style: italic;
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .hero-motto-bar {
            width: 32px;
            height: 3.5px;
            background: var(--brand-emerald);
            border-radius: 999px;
            margin-top: 0.35rem;
        }

        /* Right Form Panel - Normal, Clean, Focused */
        .form-panel {
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 2.25rem 1.25rem 2.25rem;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        /* Top Action Bar (Language Switcher) */
        .top-action-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 0.35rem;
        }

        /* High-Visibility Segmented Language Switcher */
        .lang-segmented-bar {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 9999px;
            border: 1px solid var(--border-color);
            gap: 2px;
        }

        .lang-switch-btn {
            border: none;
            background: transparent;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.2s ease;
            text-decoration: none;
            line-height: 1.2;
            font-family: inherit;
        }

        .lang-switch-btn:hover {
            color: #0f172a;
        }

        .lang-switch-btn.active {
            background: #ffffff;
            color: var(--brand-blue);
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .lang-btn {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #334155;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 9999px;
            padding: 0.28rem 0.65rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .lang-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        /* Form Center Wrap */
        .form-wrap {
            max-width: 390px;
            width: 100%;
            margin: 0 auto;
        }

        /* Normal Clean Brand Header */
        .brand-header {
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .brand-header h2 {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .brand-header p {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        /* Normal Segmented Tab (Email / Phone) */
        .tab-switcher {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 3px;
            margin-bottom: 0.85rem;
            gap: 3px;
        }

        .tab-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.45rem 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            border-radius: 6px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-muted);
            background: transparent;
        }

        .tab-btn.active {
            background: #ffffff;
            color: var(--brand-blue);
            border-color: #cbd5e1;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Normal Clean Form Fields */
        .form-field {
            margin-bottom: 0.75rem;
        }

        .field-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.3rem;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box .icon-lead {
            position: absolute;
            left: 0.85rem;
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-box input {
            width: 100%;
            height: 40px;
            padding: 0 0.85rem 0 2.5rem;
            font-size: 0.875rem;
            font-family: inherit;
            color: var(--text-dark);
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-box input:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        .input-box .btn-eye {
            position: absolute;
            right: 0.75rem;
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Remember & Forgot Row */
        .field-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.775rem;
            margin-top: -0.1rem;
            margin-bottom: 0.85rem;
        }

        .field-meta-row label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            color: #334155;
            font-weight: 500;
        }

        .field-meta-row a {
            color: var(--brand-blue);
            font-weight: 600;
            text-decoration: none;
        }

        /* Normal Solid Blue Sign In Button */
        .btn-signin {
            width: 100%;
            height: 42px;
            font-size: 0.925rem;
            font-weight: 700;
            font-family: inherit;
            color: #ffffff;
            background: var(--brand-blue);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.25);
        }

        .btn-signin:hover {
            background: var(--brand-blue-hover);
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.35);
        }

        /* Normal Compact Demo Persona Pills */
        .demo-bar {
            margin-top: 0.85rem;
            padding-top: 0.75rem;
            border-top: 1px dashed var(--border-color);
        }

        .demo-bar-title {
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.45rem;
            text-align: center;
        }

        .demo-pills-row {
            display: flex;
            justify-content: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .demo-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.28rem 0.55rem;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .demo-pill-btn:hover {
            border-color: var(--brand-blue);
            background: #eff6ff;
            color: var(--brand-blue);
        }

        .demo-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        /* Normal Footer */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.725rem;
            color: var(--text-muted);
            padding-top: 0.65rem;
            border-top: 1px solid #f1f5f9;
        }

        .form-footer a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .form-footer a:hover {
            color: var(--brand-blue);
        }

        /* Toast Popup */
        .toast-badge {
            position: absolute;
            top: 0.75rem;
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            display: none;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            z-index: 50;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -6px); }
            to { opacity: 1; transform: translate(-50%, 0); }
        }

        /* Responsive on small devices */
        @media (max-width: 768px) {
            .login-card {
                grid-template-columns: 1fr;
                max-width: 440px;
                height: auto;
                max-height: calc(100vh - 1.5rem);
            }
            .hero-banner {
                display: none !important;
            }
            .form-panel {
                padding: 1.5rem 1.25rem 1rem 1.25rem;
            }
        }
    </style>
</head>
<body>

    <!-- Main Outer Card -->
    <div class="login-card" id="loginCard">

        <!-- =================== LEFT HERO PANEL =================== -->
        <div class="hero-banner">
            <div class="hero-overlay"></div>

            <div class="hero-content">
                <!-- Top Brand Logo -->
                <div class="hero-brand">
                    <svg width="38" height="38" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 44C18 40.5 27 43 31 46.5V20.5C27 17 18 14.5 8 18V44Z" fill="#10B981"/>
                        <path d="M56 44C46 40.5 37 43 33 46.5V20.5C37 17 46 14.5 56 18V44Z" fill="#F97316"/>
                        <path d="M31 46.5C31.5 46.8 32.5 46.8 33 46.5V20.5C32.5 20.2 31.5 20.2 31 20.5V46.5Z" fill="#0284C7"/>
                        <path d="M32 24V11" stroke="#22C55E" stroke-width="3" stroke-linecap="round"/>
                        <path d="M32 17C26 17 25 12 28 10C31 10 32 14 32 17Z" fill="#22C55E"/>
                        <path d="M32 15C38 15 39 9 36 8C33 8 32 12 32 15Z" fill="#16A34A"/>
                    </svg>
                    <div>
                        <div class="hero-brand-name">SchoolMeal</div>
                        <div class="hero-brand-tag">{{ transLabel('form.hero_platform_tag', 'E-Commerce & Nutrition Platform') }}</div>
                    </div>
                </div>

                <!-- Mid Headline & Compact Feature Badges -->
                <div>
                    <h1 class="hero-headline">
                        {{ transLabel('form.hero_headline_1', 'Healthy Meals,') }}<br>
                        <span>{{ transLabel('form.hero_headline_2', 'Brighter Students') }}</span>
                    </h1>
                    <p class="hero-subtext">
                        {{ transLabel('form.hero_subtext', 'Unified management platform connecting schools, parents, kitchens and drivers for better nutrition.') }}
                    </p>

                    <!-- Compact 3-Pill Feature Strip -->
                    <div class="feature-pills">
                        <div class="feature-pill">
                            <div class="feature-pill-icon" style="background: #10B981;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"/><path d="M15 2v19"/><path d="M6 2v19"/><path d="M9 2a3 3 0 0 1-3 3 3 3 0 0 1-3-3"/></svg>
                            </div>
                            <div>
                                <div class="feature-pill-title">{{ transLabel('form.hero_feature_menus', 'Nutritious Menus') }}</div>
                                <div class="feature-pill-desc">{{ transLabel('form.hero_feature_menus_desc', 'Dietary tracking & allergen controls') }}</div>
                            </div>
                        </div>

                        <div class="feature-pill">
                            <div class="feature-pill-icon" style="background: #2563EB;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/></svg>
                            </div>
                            <div>
                                <div class="feature-pill-title">{{ transLabel('form.hero_feature_schools', 'School Operations') }}</div>
                                <div class="feature-pill-desc">{{ transLabel('form.hero_feature_schools_desc', 'Classroom manifests & student rosters') }}</div>
                            </div>
                        </div>

                        <div class="feature-pill">
                            <div class="feature-pill-icon" style="background: #9333EA;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                            </div>
                            <div>
                                <div class="feature-pill-title">{{ transLabel('form.hero_feature_dispatch', 'Dispatch Fleet') }}</div>
                                <div class="feature-pill-desc">{{ transLabel('form.hero_feature_dispatch_desc', 'Route optimization & on-time deliveries') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Motto -->
                <div>
                    <div class="hero-motto">&ldquo;{{ transLabel('form.hero_motto', 'Good Nutrition Today, A Brighter Tomorrow') }}&rdquo;</div>
                    <div class="hero-motto-bar"></div>
                </div>
            </div>
        </div>

        <!-- =================== RIGHT FORM PANEL =================== -->
        <div class="form-panel">

            <!-- Top Action Bar (Fast 1-Click Segmented Language Switcher) -->
            <div class="top-action-bar">
                @php
                    $activeLocale = app()->getLocale();
                @endphp
                <div class="lang-segmented-bar" role="group" aria-label="{{ transLabel('form.select_language', 'Select Language') }}">
                    <form method="POST" action="{{ route('locale.switch') }}" style="margin: 0; display: inline;">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <button type="submit" class="lang-switch-btn {{ $activeLocale === 'en' ? 'active' : '' }}" title="Switch to English" id="switchLangEn">
                            <img src="{{ asset('images/flags/gb.svg') }}" alt="UK Flag" style="width: 18px; height: 12px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15); display: inline-block; vertical-align: middle;">
                            <span>English</span>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('locale.switch') }}" style="margin: 0; display: inline;">
                        @csrf
                        <input type="hidden" name="locale" value="km">
                        <button type="submit" class="lang-switch-btn {{ $activeLocale === 'km' ? 'active' : '' }}" title="ប្តូរទៅភាសាខ្មែរ" id="switchLangKm">
                            <img src="{{ asset('images/flags/km.svg') }}" alt="Cambodia Flag" style="width: 18px; height: 12px; border-radius: 2px; object-fit: cover; box-shadow: 0 1px 2px rgba(0,0,0,0.15); display: inline-block; vertical-align: middle;">
                            <span style="font-family: 'Hanuman', serif;">ភាសាខ្មែរ</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Central Form Content -->
            <div class="form-wrap">

                <!-- Normal Brand Header -->
                <div class="brand-header">
                    <div style="display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.35rem;">
                        <svg width="42" height="42" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 44C18 40.5 27 43 31 46.5V20.5C27 17 18 14.5 8 18V44Z" fill="#10B981"/>
                            <path d="M56 44C46 40.5 37 43 33 46.5V20.5C37 17 46 14.5 56 18V44Z" fill="#F97316"/>
                            <path d="M31 46.5C31.5 46.8 32.5 46.8 33 46.5V20.5C32.5 20.2 31.5 20.2 31 20.5V46.5Z" fill="#0284C7"/>
                            <path d="M32 24V11" stroke="#22C55E" stroke-width="3" stroke-linecap="round"/>
                            <path d="M32 17C26 17 25 12 28 10C31 10 32 14 32 17Z" fill="#22C55E"/>
                            <path d="M32 15C38 15 39 9 36 8C33 8 32 12 32 15Z" fill="#16A34A"/>
                        </svg>
                    </div>
                    <h2>{{ transLabel('form.sign_in', 'Sign in to your account') }}</h2>
                    <p>{{ transLabel('form.sign_in_subtitle', 'SchoolMeal Admin & Management Portal') }}</p>
                </div>

                <!-- Normal Segmented Tab (Email / Phone) -->
                <div class="tab-switcher">
                    <button type="button" class="tab-btn active" id="tabEmail" onclick="switchLoginTab('email')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <span>{{ transLabel('form.email_login', 'Email Login') }}</span>
                    </button>
                    <button type="button" class="tab-btn" id="tabPhone" onclick="switchLoginTab('phone')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span>{{ transLabel('form.phone_login', 'Phone Login') }}</span>
                    </button>
                </div>

                <!-- Normal Login Form -->
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <input type="hidden" name="login" id="loginField" value="{{ old('login', 'admin@schoolmeal.test') }}">

                    <!-- Email Input -->
                    <div class="form-field" id="groupEmail">
                        <label class="field-label" for="emailInput">{{ transLabel('form.email_address', 'Email Address') }}</label>
                        <div class="input-box">
                            <span class="icon-lead">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </span>
                            <input
                                type="email"
                                id="emailInput"
                                placeholder="{{ transLabel('form.email_placeholder', 'admin@schoolmeal.test') }}"
                                value="{{ old('login', 'admin@schoolmeal.test') }}"
                                autocomplete="email"
                                oninput="syncLoginInput(this.value)"
                                required
                            >
                        </div>
                    </div>

                    <!-- Phone Input (Hidden by default) -->
                    <div class="form-field" id="groupPhone" style="display: none;">
                        <label class="field-label" for="phoneInput">{{ transLabel('form.phone_number', 'Phone Number') }}</label>
                        <div class="input-box">
                            <span class="icon-lead">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </span>
                            <input
                                type="text"
                                id="phoneInput"
                                placeholder="{{ transLabel('form.phone_placeholder', '+855 12 000 001') }}"
                                autocomplete="tel"
                                oninput="syncLoginInput(this.value)"
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="form-field">
                        <label class="field-label" for="passwordInput">{{ transLabel('form.password', 'Password') }}</label>
                        <div class="input-box">
                            <span class="icon-lead">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input
                                type="password"
                                name="password"
                                id="passwordInput"
                                placeholder="••••••••"
                                value="Password@123"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="btn-eye" id="togglePasswordBtn" onclick="togglePasswordVisibility()" aria-label="Toggle password view">
                                <svg id="eyeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="eyeSlashIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember & Forgot Password -->
                    <div class="field-meta-row">
                        <label for="remember" style="cursor: pointer;">
                            <input type="checkbox" name="remember" id="remember" value="1" style="width: 15px; height: 15px; accent-color: var(--brand-blue); cursor: pointer;" {{ (!session()->hasOldInput() || old('remember')) ? 'checked' : '' }}>
                            <span>{{ transLabel('form.remember_me', 'Remember me') }}</span>
                        </label>
                        <a href="javascript:void(0)" onclick="showToast('{{ addslashes(transLabel('form.password_reset_sent', 'Password reset link sent to admin.')) }}')">{{ transLabel('form.forgot_password', 'Forgot password?') }}</a>
                    </div>

                    <!-- Normal Sign In Button -->
                    <button type="submit" class="btn-signin" id="submitBtn">
                        <span>{{ transLabel('form.sign_in_button', 'Sign In') }}</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>

                <!-- Normal Compact 1-Click Fast Demo Persona Pills -->
                <div class="demo-bar">
                    <div class="demo-bar-title">{{ transLabel('form.platform_access_for', 'Platform Access For') }}</div>
                    <div class="demo-pills-row">
                        <button type="button" class="demo-pill-btn" id="pillAdmin" onclick="autofillPersona('admin@schoolmeal.test', '+85512000001', 'Password@123', '{{ addslashes(transLabel('form.role_super_admin', 'Super Admin')) }}')" title="Admin: admin@schoolmeal.test / Password@123">
                            <span class="demo-dot" style="background: #2563eb;"></span>
                            <span>{{ transLabel('form.role_admin', 'Admin') }}</span>
                        </button>
                        <button type="button" class="demo-pill-btn" id="pillSchool" onclick="autofillPersona('school@schoolmeal.test', '+85512000002', 'Password@123', '{{ addslashes(transLabel('form.role_school_admin', 'School Admin')) }}')" title="School: school@schoolmeal.test / Password@123">
                            <span class="demo-dot" style="background: #0284c7;"></span>
                            <span>{{ transLabel('form.role_school', 'School') }}</span>
                        </button>
                        <button type="button" class="demo-pill-btn" id="pillRestaurantAdmin" onclick="autofillPersona('merchant@schoolmeal.test', '+85512000004', 'Password@123', '{{ addslashes(transLabel('form.role_restaurant_admin', 'Restaurant Admin')) }}')" title="Restaurant Admin: merchant@schoolmeal.test / Password@123">
                            <span class="demo-dot" style="background: #ea580c;"></span>
                            <span>{{ transLabel('form.role_restaurant_admin', 'Restaurant Admin') }}</span>
                        </button>
                        <button type="button" class="demo-pill-btn" id="pillKitchen" onclick="autofillPersona('kitchen@schoolmeal.test', '+85512000009', 'Password@123', '{{ addslashes(transLabel('form.role_kitchen', 'Kitchen')) }}')" title="Kitchen Staff: kitchen@schoolmeal.test / Password@123">
                            <span class="demo-dot" style="background: #d97706;"></span>
                            <span>{{ transLabel('form.role_kitchen', 'Kitchen') }}</span>
                        </button>
                        <button type="button" class="demo-pill-btn" id="pillDriver" onclick="autofillPersona('driver@schoolmeal.test', '+85512000005', 'Password@123', '{{ addslashes(transLabel('form.role_driver', 'Driver')) }}')" title="Driver: driver@schoolmeal.test / Password@123">
                            <span class="demo-dot" style="background: #9333ea;"></span>
                            <span>{{ transLabel('form.role_driver', 'Driver') }}</span>
                        </button>
                        <button type="button" class="demo-pill-btn" id="pillFinance" onclick="autofillPersona('finance@schoolmeal.test', '+85512000006', 'Password@123', '{{ addslashes(transLabel('form.role_finance', 'Finance')) }}')" title="Finance: finance@schoolmeal.test / Password@123">
                            <span class="demo-dot" style="background: #16a34a;"></span>
                            <span>{{ transLabel('form.role_finance', 'Finance') }}</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Normal Footer -->
            <div class="form-footer">
                <div>&copy; 2026 SchoolMeal</div>
                <div style="display: flex; gap: 0.85rem;">
                    <a href="javascript:void(0)" onclick="showToast('{{ addslashes(transLabel('form.privacy_viewed', 'Privacy Policy viewed')) }}')">{{ transLabel('form.privacy', 'Privacy') }}</a>
                    <a href="javascript:void(0)" onclick="showToast('{{ addslashes(transLabel('form.terms_viewed', 'Terms of Service viewed')) }}')">{{ transLabel('form.terms', 'Terms') }}</a>
                    <a href="mailto:support@schoolmeal.test">{{ transLabel('form.support', 'Support') }}</a>
                </div>
            </div>

        </div>

    </div>

    <!-- Interactive Client JavaScript -->
    <script>
        let currentTab = 'email';

        function switchLoginTab(tab) {
            currentTab = tab;
            const tabEmail = document.getElementById('tabEmail');
            const tabPhone = document.getElementById('tabPhone');
            const groupEmail = document.getElementById('groupEmail');
            const groupPhone = document.getElementById('groupPhone');
            const emailInput = document.getElementById('emailInput');
            const phoneInput = document.getElementById('phoneInput');
            const loginField = document.getElementById('loginField');

            if (tab === 'email') {
                tabEmail.classList.add('active');
                tabPhone.classList.remove('active');
                groupEmail.style.display = 'block';
                groupPhone.style.display = 'none';
                emailInput.required = true;
                phoneInput.required = false;
                loginField.value = emailInput.value;
            } else {
                tabPhone.classList.add('active');
                tabEmail.classList.remove('active');
                groupPhone.style.display = 'block';
                groupEmail.style.display = 'none';
                phoneInput.required = true;
                emailInput.required = false;
                loginField.value = phoneInput.value;
            }
        }

        function syncLoginInput(val) {
            document.getElementById('loginField').value = val;
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'block';
                eyeSlashIcon.style.display = 'none';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'none';
                eyeSlashIcon.style.display = 'block';
            }
        }

        const toastLoadedTemplate = "{{ transLabel('form.credentials_loaded', 'Loaded :role credentials!') }}";

        function autofillPersona(email, arg2, arg3, arg4) {
            let phone = '';
            let password = 'Password@123';
            let roleName = '';

            if (arg4 !== undefined) {
                // (email, phone, password, roleName)
                phone = arg2;
                password = arg3;
                roleName = arg4;
            } else {
                // (email, password, roleName)
                password = arg2;
                roleName = arg3;
            }

            const emailInput = document.getElementById('emailInput');
            const phoneInput = document.getElementById('phoneInput');
            const passwordInput = document.getElementById('passwordInput');

            if (emailInput) emailInput.value = email;
            if (phoneInput && phone) phoneInput.value = phone;
            if (passwordInput) passwordInput.value = password;

            if (currentTab === 'phone' && phone) {
                syncLoginInput(phone);
            } else {
                if (currentTab === 'phone' && !phone) {
                    switchLoginTab('email');
                }
                syncLoginInput(email);
            }

            const msg = toastLoadedTemplate.replace(':role', roleName);
            if (window.Toast && typeof window.Toast.success === 'function') {
                window.Toast.success(msg);
            } else if (typeof window.showToast === 'function') {
                window.showToast(msg, 'success');
            }
        }
    </script>
    <x-toast-container />
</body>
</html>

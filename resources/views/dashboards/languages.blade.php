@extends('layouts.master_super_admin')

@section('title', transLabel('menu.manage_languages', 'Manage Languages'))

@section('content')

    <!-- Header & Breadcrumbs -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.775rem; color: #64748b; font-weight: 600; margin-bottom: 0.35rem;">
                <a href="{{ route('admin.dashboard') }}" style="color: #64748b; text-decoration: none;">{{ transLabel('menu.dashboard', 'Dashboard') }}</a>
                <span>/</span>
                <span>{{ transLabel('menu.system', 'System') }}</span>
                <span>/</span>
                <span style="color: #1D4ED8;">{{ transLabel('menu.manage_languages', 'Manage Languages') }}</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: #0f172a; letter-spacing: -0.025em; line-height: 1.15;">
                {{ transLabel('menu.manage_languages', 'Manage Languages & Dictionaries') }}
            </h1>
            <p style="font-size: 0.825rem; color: #64748b; margin-top: 0.25rem;">
                Standardized JSON language dictionaries managed via <code>TraitLanguage</code>. Shared across Laravel Blade, Next.js web portal, and Mobile apps.
            </p>
        </div>

        <!-- Top Action Buttons -->
        <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
            <a href="{{ url('/api/v1/translations/km') }}" target="_blank" class="btn-api-preview" style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.55rem 0.95rem; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 9px; font-size: 0.8125rem; font-weight: 700; color: #334155; text-decoration: none; transition: all 0.2s;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                <span>Preview Mobile API (JSON)</span>
            </a>

            <button type="button" onclick="openAddKeyModal()" style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.55rem 1.15rem; background: #1D4ED8; border: none; border-radius: 9px; font-size: 0.8125rem; font-weight: 700; color: #ffffff; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(29, 78, 216, 0.25);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Add Translation Key</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        
        <!-- Total Keys -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #1D4ED8; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div>
                <div style="font-size: 0.725rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Total Dictionary Keys</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1.1; margin-top: 2px;">{{ $stats['total_keys'] }}</div>
                <div style="font-size: 0.7rem; color: #10B981; font-weight: 600; margin-top: 2px;">Stored in lang/*.json</div>
            </div>
        </div>

        <!-- English Locale -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                🇬🇧
            </div>
            <div>
                <div style="font-size: 0.725rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">English (en.json)</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1.1; margin-top: 2px;">{{ $stats['en_count'] }} <span style="font-size: 0.8rem; color: #10b981; font-weight: 700;">(100%)</span></div>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 2px;">Default Base Language</div>
            </div>
        </div>

        <!-- Khmer Locale -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fdf2f8; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                🇰🇭
            </div>
            <div>
                <div style="font-size: 0.725rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Khmer (km.json)</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1.1; margin-top: 2px;">{{ $stats['km_count'] }} <span style="font-size: 0.8rem; color: #10b981; font-weight: 700;">({{ $stats['completion_pct'] }}%)</span></div>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 2px;">ភាសាខ្មែរ រៀបរយ</div>
            </div>
        </div>

        <!-- Cross-Platform Export -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #f5f3ff; display: flex; align-items: center; justify-content: center; color: #7c3aed; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            </div>
            <div>
                <div style="font-size: 0.725rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Client Platforms</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #0f172a; line-height: 1.1; margin-top: 2px;">Next.js & Mobile</div>
                <div style="font-size: 0.7rem; color: #7c3aed; font-weight: 600; margin-top: 2px;">REST API Synchronized</div>
            </div>
        </div>

    </div>

    <!-- API & Developer Integration Banner -->
    <div style="background: linear-gradient(135deg, #0b1528 0%, #1e293b 100%); border-radius: 14px; padding: 1.25rem 1.5rem; color: #ffffff; margin-bottom: 1.75rem; box-shadow: 0 4px 16px rgba(11, 21, 40, 0.15);">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <span style="background: #2563EB; color: #ffffff; font-size: 0.675rem; font-weight: 800; padding: 0.2rem 0.55rem; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.04em;">TraitLanguage & API</span>
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #ffffff;">Universal JSON Dictionary Endpoints</h3>
                </div>
                <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.35rem; max-width: 780px;">
                    External applications can consume translations instantly. For Next.js (via <code>next-intl</code> or <code>i18next</code>) and Flutter / React Native mobile apps, query <code>GET /api/v1/translations/{locale}</code> or download raw JSON files located in <code>lang/*.json</code>.
                </p>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ url('/api/v1/translations/en') }}" target="_blank" style="padding: 0.45rem 0.85rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #ffffff; font-size: 0.775rem; font-weight: 600; text-decoration: none; transition: background 0.15s;">
                    GET /api/v1/translations/en
                </a>
                <a href="{{ url('/api/v1/translations/km') }}" target="_blank" style="padding: 0.45rem 0.85rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #38bdf8; font-size: 0.775rem; font-weight: 600; text-decoration: none; transition: background 0.15s;">
                    GET /api/v1/translations/km
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Bar & Search Card -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; margin-bottom: 1.25rem;">
        <form method="GET" action="{{ route('admin.languages.index') }}" id="filterForm">
            <input type="hidden" name="category" id="selectedCategoryInput" value="{{ $selectedCategory }}">
            
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <!-- Category Pills -->
                <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                    <span style="font-size: 0.775rem; font-weight: 700; color: #64748b; margin-right: 0.25rem;">Category:</span>
                    @php
                        $categories = [
                            'all' => 'All Categories',
                            'menu' => 'Menu (menu.*)',
                            'form' => 'Forms (form.*)',
                            'dashboard' => 'Dashboard (dashboard.*)',
                            'common' => 'Common (common.*)',
                        ];
                    @endphp
                    @foreach($categories as $catKey => $catLabel)
                        <button type="button" onclick="setCategoryFilter('{{ $catKey }}')" style="padding: 0.4rem 0.75rem; border-radius: 8px; font-size: 0.775rem; font-weight: 600; border: 1px solid {{ $selectedCategory === $catKey ? '#2563EB' : '#e2e8f0' }}; background: {{ $selectedCategory === $catKey ? '#eff6ff' : '#ffffff' }}; color: {{ $selectedCategory === $catKey ? '#1D4ED8' : '#475569' }}; cursor: pointer; transition: all 0.15s ease;">
                            {{ $catLabel }}
                        </button>
                    @endforeach
                </div>

                <!-- Live Search Box -->
                <div style="display: flex; align-items: center; gap: 0.5rem; width: 340px; max-width: 100%;">
                    <div style="position: relative; width: 100%;">
                        <input
                            type="text"
                            name="search"
                            id="searchInput"
                            value="{{ $search }}"
                            placeholder="Search by key, English or Khmer..."
                            style="width: 100%; height: 38px; padding: 0 2.25rem 0 2.25rem; font-size: 0.8125rem; font-family: inherit; color: #0f172a; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.2s;"
                            onkeyup="liveClientFilter()"
                        >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" style="position: absolute; left: 0.75rem; top: 11px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        @if($search !== '')
                            <a href="{{ route('admin.languages.index', ['category' => $selectedCategory]) }}" style="position: absolute; right: 0.75rem; top: 11px; color: #94a3b8; text-decoration: none;" title="Clear search">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </a>
                        @endif
                    </div>
                    <button type="submit" style="height: 38px; padding: 0 0.95rem; background: #0f172a; color: #ffffff; border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer; white-space: nowrap;">
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Main Translation Table Card -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);">
        
        <form method="POST" action="{{ route('admin.languages.bulk') }}" id="bulkSaveForm">
            @csrf

            <!-- Table Header Bar -->
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #eef2f6; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; background: #ffffff;">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div style="font-size: 0.925rem; font-weight: 800; color: #0f172a;">
                        Dictionary Translation Entries
                    </div>
                    <span id="filteredCountBadge" style="background: #f1f5f9; color: #475569; font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.55rem; border-radius: 999px;">
                        Showing {{ count($translationList) }} entries
                    </span>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <button type="button" onclick="resetForm()" style="padding: 0.45rem 0.85rem; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.775rem; font-weight: 600; color: #475569; cursor: pointer;">
                        Discard Changes
                    </button>
                    <button type="submit" style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.45rem 1.15rem; background: #10B981; border: none; border-radius: 8px; font-size: 0.775rem; font-weight: 700; color: #ffffff; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        <span>Save All to JSON Files</span>
                    </button>
                </div>
            </div>

            <!-- Table View -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;" id="translationTable">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0; color: #64748b; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em;">
                            <th style="padding: 0.85rem 1.25rem; width: 28%;">Translation Key / Tag</th>
                            <th style="padding: 0.85rem 1.25rem; width: 32%;">🇬🇧 English Translation (en.json)</th>
                            <th style="padding: 0.85rem 1.25rem; width: 32%;">🇰🇭 Khmer Translation (km.json)</th>
                            <th style="padding: 0.85rem 1rem; width: 8%; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($translationList as $row)
                            <tr class="translation-row" data-key="{{ strtolower($row['key']) }}" data-en="{{ strtolower($row['en']) }}" data-km="{{ strtolower($row['km']) }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                                
                                <!-- Key Column -->
                                <td style="padding: 0.75rem 1.25rem; vertical-align: top;">
                                    <div style="display: flex; align-items: center; gap: 0.45rem;">
                                        @php
                                            $badgeColor = match($row['category']) {
                                                'menu' => ['bg' => '#eff6ff', 'text' => '#1D4ED8'],
                                                'form' => ['bg' => '#fdf2f8', 'text' => '#db2777'],
                                                'dashboard' => ['bg' => '#ecfdf5', 'text' => '#059669'],
                                                'common' => ['bg' => '#f5f3ff', 'text' => '#7c3aed'],
                                                default => ['bg' => '#f1f5f9', 'text' => '#475569'],
                                            };
                                        @endphp
                                        <span style="background: {{ $badgeColor['bg'] }}; color: {{ $badgeColor['text'] }}; font-size: 0.65rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 6px; text-transform: uppercase;">
                                            {{ $row['category'] }}
                                        </span>
                                        <code style="font-family: monospace; font-size: 0.825rem; font-weight: 700; color: #0f172a; word-break: break-all;">
                                            {{ $row['key'] }}
                                        </code>
                                    </div>
                                    <div style="margin-top: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <button type="button" onclick="copySnippet('transLabel(\'{{ $row['key'] }}\')')" style="background: none; border: none; font-size: 0.675rem; color: #64748b; cursor: pointer; text-decoration: underline; padding: 0;" title="Copy Blade code">
                                            Copy Blade Code
                                        </button>
                                        <span style="color: #cbd5e1;">&bull;</span>
                                        <button type="button" onclick="copySnippet('{{ $row['key'] }}')" style="background: none; border: none; font-size: 0.675rem; color: #64748b; cursor: pointer; text-decoration: underline; padding: 0;" title="Copy raw key">
                                            Copy Key
                                        </button>
                                    </div>
                                </td>

                                <!-- English Input -->
                                <td style="padding: 0.65rem 1.25rem; vertical-align: top;">
                                    <textarea
                                        name="translations[{{ $row['key'] }}][en]"
                                        rows="2"
                                        style="width: 100%; padding: 0.5rem 0.65rem; font-size: 0.8125rem; font-family: inherit; color: #0f172a; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; resize: vertical; min-height: 38px; transition: border-color 0.2s;"
                                        onfocus="this.style.borderColor='#2563EB'; this.style.boxShadow='0 0 0 2px rgba(37,99,235,0.1)';"
                                        onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                                    >{{ $row['en'] }}</textarea>
                                </td>

                                <!-- Khmer Input -->
                                <td style="padding: 0.65rem 1.25rem; vertical-align: top;">
                                    <textarea
                                        name="translations[{{ $row['key'] }}][km]"
                                        rows="2"
                                        style="width: 100%; padding: 0.5rem 0.65rem; font-size: 0.925rem; line-height: 1.5; font-family: 'Hanuman', var(--font-sans); color: #0f172a; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; resize: vertical; min-height: 38px; transition: border-color 0.2s;"
                                        onfocus="this.style.borderColor='#10B981'; this.style.boxShadow='0 0 0 2px rgba(16,185,129,0.1)';"
                                        onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                                    >{{ $row['km'] }}</textarea>
                                </td>

                                <!-- Actions -->
                                <td style="padding: 0.65rem 1rem; text-align: center; vertical-align: middle;">
                                    <button
                                        type="button"
                                        onclick="saveSingleKey('{{ $row['key'] }}')"
                                        style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.725rem; font-weight: 600; color: #334155; cursor: pointer; transition: all 0.15s;"
                                        title="Quick Save Row"
                                    >
                                        Save
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 2.5rem 1rem; text-align: center; color: #64748b;">
                                    <div style="font-size: 1.1rem; font-weight: 700; color: #0f172a;">No translation entries found</div>
                                    <div style="font-size: 0.8rem; margin-top: 4px;">Try refining your search keyword or selected category.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div style="padding: 0.85rem 1.25rem; background: #f8fafc; border-top: 1px solid #eef2f6; display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: #64748b;">
                <div>Files updated on save: <code>lang/en.json</code> and <code>lang/km.json</code></div>
                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.45rem 1.15rem; background: #10B981; border: none; border-radius: 8px; font-size: 0.775rem; font-weight: 700; color: #ffffff; cursor: pointer;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <span>Save All Changes</span>
                </button>
            </div>

        </form>
    </div>

    <!-- Hidden Single Update Form for row-level submission -->
    <form method="POST" action="{{ route('admin.languages.update') }}" id="singleKeyForm" style="display: none;">
        @csrf
        <input type="hidden" name="key" id="singleKeyInput">
        <input type="hidden" name="en" id="singleEnInput">
        <input type="hidden" name="km" id="singleKmInput">
    </form>

    <!-- Modal: Add New Translation Key -->
    <div id="addKeyModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 200; align-items: center; justify-content: center; padding: 1rem;">
        <div style="background: #ffffff; border-radius: 14px; max-width: 500px; width: 100%; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); overflow: hidden; animation: modalFadeIn 0.2s ease;">
            
            <div style="padding: 1.15rem 1.5rem; border-bottom: 1px solid #eef2f6; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #1D4ED8;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </div>
                    <div style="font-size: 1rem; font-weight: 800; color: #0f172a;">Add New Translation Key</div>
                </div>
                <button type="button" onclick="closeAddKeyModal()" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0.25rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.languages.update') }}" style="padding: 1.5rem;">
                @csrf

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.775rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Key Identifier (e.g. menu.driver_logs or form.parent_phone)</label>
                    <input
                        type="text"
                        name="key"
                        required
                        placeholder="menu.orders_analytics"
                        style="width: 100%; height: 40px; padding: 0 0.85rem; font-family: monospace; font-size: 0.85rem; color: #0f172a; border: 1.5px solid #cbd5e1; border-radius: 8px; outline: none;"
                        onfocus="this.style.borderColor='#2563EB';"
                        onblur="this.style.borderColor='#cbd5e1';"
                    >
                    <div style="font-size: 0.7rem; color: #64748b; margin-top: 3px;">Prefix with namespace: <code>menu.</code>, <code>form.</code>, <code>dashboard.</code>, or <code>common.</code></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.775rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">🇬🇧 English Translation</label>
                    <input
                        type="text"
                        name="en"
                        required
                        placeholder="Orders Analytics"
                        style="width: 100%; height: 40px; padding: 0 0.85rem; font-size: 0.875rem; color: #0f172a; border: 1.5px solid #cbd5e1; border-radius: 8px; outline: none;"
                    >
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.775rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">🇰🇭 Khmer Translation</label>
                    <input
                        type="text"
                        name="km"
                        placeholder="ការវិភាគការបញ្ជាទិញ"
                        style="width: 100%; height: 40px; padding: 0 0.85rem; font-size: 0.875rem; color: #0f172a; border: 1.5px solid #cbd5e1; border-radius: 8px; outline: none;"
                    >
                </div>

                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.65rem;">
                    <button type="button" onclick="closeAddKeyModal()" style="padding: 0.55rem 1rem; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; color: #475569; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="padding: 0.55rem 1.25rem; background: #1D4ED8; border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; color: #ffffff; cursor: pointer; box-shadow: 0 2px 8px rgba(29, 78, 216, 0.25);">
                        Save New Key
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Floating Copy Toast -->
    <div id="copyToast" style="display: none; position: fixed; bottom: 2rem; right: 2rem; background: #0f172a; color: #ffffff; padding: 0.65rem 1.15rem; border-radius: 10px; font-size: 0.8125rem; font-weight: 600; z-index: 300; box-shadow: 0 10px 25px rgba(0,0,0,0.25); align-items: center; gap: 0.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span id="copyToastText">Copied to clipboard!</span>
    </div>

@endsection

@push('scripts')
<script>
    // 1. Modal Toggle
    function openAddKeyModal() {
        const modal = document.getElementById('addKeyModal');
        modal.style.display = 'flex';
    }

    function closeAddKeyModal() {
        const modal = document.getElementById('addKeyModal');
        modal.style.display = 'none';
    }

    // Close on Escape or Outside click
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAddKeyModal();
    });

    document.getElementById('addKeyModal').addEventListener('click', function(e) {
        if (e.target === this) closeAddKeyModal();
    });

    // 2. Category Filter Switcher
    function setCategoryFilter(category) {
        document.getElementById('selectedCategoryInput').value = category;
        document.getElementById('filterForm').submit();
    }

    // 3. Client-side Realtime Instant Table Filter
    function liveClientFilter() {
        const query = document.getElementById('searchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.translation-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const key = row.getAttribute('data-key') || '';
            const en = row.getAttribute('data-en') || '';
            const km = row.getAttribute('data-km') || '';

            if (query === '' || key.includes(query) || en.includes(query) || km.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const badge = document.getElementById('filteredCountBadge');
        if (badge) {
            badge.textContent = `Showing ${visibleCount} entries`;
        }
    }

    // 4. Quick Save Single Key
    function saveSingleKey(key) {
        const enField = document.querySelector(`textarea[name="translations[${key}][en]"]`);
        const kmField = document.querySelector(`textarea[name="translations[${key}][km]"]`);

        if (!enField) return;

        document.getElementById('singleKeyInput').value = key;
        document.getElementById('singleEnInput').value = enField.value;
        document.getElementById('singleKmInput').value = kmField ? kmField.value : '';

        document.getElementById('singleKeyForm').submit();
    }

    // 5. Copy snippet to clipboard with toast
    function copySnippet(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                showCopyToast(`Copied "${text}"`);
            });
        }
    }

    function showCopyToast(msg) {
        const toast = document.getElementById('copyToast');
        const text = document.getElementById('copyToastText');
        text.textContent = msg;
        toast.style.display = 'flex';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 2200);
    }

    // 6. Reset Form
    function resetForm() {
        if (confirm('Discard any unsaved changes in the table?')) {
            window.location.reload();
        }
    }
</script>
@endpush

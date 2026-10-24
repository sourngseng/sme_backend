@extends('layouts.master_super_admin')

@section('header_title')
<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #FF6C37 0%, #EA580C 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 18 22 12 16 6"></polyline>
                <polyline points="8 6 2 12 8 18"></polyline>
            </svg>
        </div>
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2;">
                REST API Studio &amp; Interactive Tester
            </h1>
            <p style="font-size: 0.85rem; color: #64748b; margin: 2px 0 0 0;">
                Postman-style API explorer &amp; live sandbox for Frontend (Next.js) &amp; Mobile (Flutter) development.
            </p>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 9999px; background: #ecfdf5; border: 1px solid #a7f3d0; font-size: 0.78rem; font-weight: 700; color: #065f46;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; animation: pulse 2s infinite;"></span>
            {{ $totalEndpoints }} APIs Live &amp; Ready
        </span>
        <button type="button" onclick="resetConsole()" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; font-size: 0.82rem; font-weight: 600; border-radius: 8px; background: #ffffff; border: 1px solid #cbd5e1; cursor: pointer;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
            Reset
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="api-studio-container" style="display: grid; grid-template-columns: 360px 1fr; gap: 20px; align-items: start; margin-top: 10px;">

    <!-- Left Sidebar: API Collections & Persona Switcher -->
    <div class="api-sidebar-panel" style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.03); overflow: hidden; display: flex; flex-direction: column; max-height: calc(100vh - 140px); position: sticky; top: 85px;">
        
        <!-- Persona Quick Authenticator -->
        <div style="padding: 14px; border-bottom: 1px solid #f1f5f9; background: #fafafa;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <label style="font-size: 0.76rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Auth Persona Switcher
                </label>
                <span id="activePersonaBadge" style="font-size: 0.7rem; font-weight: 700; color: #1e3a8a; background: #dbeafe; padding: 2px 8px; border-radius: 9999px;">
                    Super Admin
                </span>
            </div>
            <select id="personaSelect" onchange="switchPersona(this.value)" style="width: 100%; padding: 8px 10px; font-size: 0.82rem; font-weight: 600; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                @foreach($personas as $key => $persona)
                    <option value="{{ $key }}" {{ $key === 'super_admin' ? 'selected' : '' }}>
                        {{ $persona['label'] }} ({{ $persona['email'] }})
                    </option>
                @endforeach
                <option value="none">No Auth (Public / Guest)</option>
            </select>
        </div>

        <!-- Search Bar -->
        <div style="padding: 12px 14px; border-bottom: 1px solid #f1f5f9;">
            <div style="position: relative;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="apiSearchInput" oninput="filterEndpoints(this.value)" placeholder="Filter endpoints, paths, tags..." style="width: 100%; padding: 8px 10px 8px 32px; font-size: 0.82rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; box-sizing: border-box; transition: all 0.2s;">
            </div>
        </div>

        <!-- Collections Accordion List -->
        <div class="api-collections-tree" id="apiCollectionsList" style="overflow-y: auto; padding: 10px 8px; flex: 1;">
            @foreach($categories as $catIndex => $category)
                <div class="category-block" data-category="{{ $category['id'] }}" style="margin-bottom: 8px;">
                    <button type="button" onclick="toggleCategory('{{ $category['id'] }}')" style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; background: transparent; border: none; border-radius: 8px; cursor: pointer; text-align: left; font-size: 0.8rem; font-weight: 700; color: #334155; transition: background 0.15s;">
                        <span style="display: flex; align-items: center; gap: 8px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <span style="color: #64748b; font-size: 0.75rem;">▼</span>
                            <span>{{ $category['name'] }}</span>
                        </span>
                        <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 2px 7px; border-radius: 9999px;">
                            {{ count($category['endpoints']) }}
                        </span>
                    </button>
                    <div id="cat_items_{{ $category['id'] }}" class="category-items" style="display: flex; flex-direction: column; gap: 3px; padding-left: 10px; margin-top: 4px;">
                        @foreach($category['endpoints'] as $endpoint)
                            <div class="endpoint-nav-item" 
                                 id="nav_item_{{ $endpoint['id'] }}"
                                 onclick="selectEndpoint({{ json_encode($endpoint) }})"
                                 data-method="{{ $endpoint['method'] }}"
                                 data-path="{{ $endpoint['path'] }}"
                                 data-name="{{ $endpoint['name'] }}"
                                 style="display: flex; align-items: center; gap: 8px; padding: 6px 10px; border-radius: 7px; cursor: pointer; border: 1px solid transparent; font-size: 0.78rem; transition: all 0.15s;">
                                <span class="method-tag method-{{ strtolower($endpoint['method']) }}" style="font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 4px; min-width: 44px; text-align: center; text-transform: uppercase;">
                                    {{ $endpoint['method'] }}
                                </span>
                                <span style="font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                    {{ $endpoint['name'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right Workspace: Interactive Request Builder & Response Console -->
    <div class="api-workspace-panel" style="display: flex; flex-direction: column; gap: 16px;">

        <!-- Main Request Builder Card -->
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.03); padding: 18px;">
            
            <!-- Target Meta Info -->
            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; gap: 12px;">
                <div>
                    <h2 id="currentEndpointTitle" style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">
                        Select an API to Test
                    </h2>
                    <p id="currentEndpointDesc" style="font-size: 0.82rem; color: #64748b; margin: 3px 0 0 0;">
                        Choose an endpoint from the left collection to inspect schemas and execute live calls.
                    </p>
                </div>
                <div id="endpointBadgeRow" style="display: flex; gap: 6px; align-items: center;">
                    <span id="authRequirementBadge" style="font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;">
                        Auth Required
                    </span>
                </div>
            </div>

            <!-- URL & Method Bar -->
            <div style="display: flex; align-items: stretch; gap: 8px; margin-bottom: 16px;">
                <select id="requestMethodSelect" style="width: 110px; padding: 10px; font-size: 0.85rem; font-weight: 800; border-radius: 8px; border: 2px solid #cbd5e1; background: #ffffff; cursor: pointer; outline: none;">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="PATCH">PATCH</option>
                    <option value="DELETE">DELETE</option>
                </select>
                <div style="flex: 1; position: relative;">
                    <input type="text" id="requestUrlInput" value="/api/v1/auth/me" placeholder="/api/v1/..." style="width: 100%; height: 100%; padding: 10px 14px; font-size: 0.9rem; font-family: 'Consolas', monospace; font-weight: 600; color: #0f172a; background: #f8fafc; border: 2px solid #cbd5e1; border-radius: 8px; outline: none; box-sizing: border-box; transition: border-color 0.2s;">
                </div>
                <button type="button" id="sendRequestBtn" onclick="executeApiCall()" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 24px; font-size: 0.88rem; font-weight: 800; border-radius: 8px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #ffffff; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); transition: all 0.2s;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    <span>Send</span>
                </button>
            </div>

            <!-- Request Configuration Tabs -->
            <div class="request-tabs-wrapper" style="border-top: 1px solid #f1f5f9; padding-top: 12px;">
                <div style="display: flex; gap: 6px; margin-bottom: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                    <button type="button" onclick="switchRequestTab('headers')" id="tabBtn_headers" class="req-tab-btn active" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; background: #dbeafe; color: #1e40af;">Headers (<span id="headerCount">3</span>)</button>
                    <button type="button" onclick="switchRequestTab('body')" id="tabBtn_body" class="req-tab-btn" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; background: transparent; color: #64748b;">Body (JSON)</button>
                    <button type="button" onclick="switchRequestTab('params')" id="tabBtn_params" class="req-tab-btn" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; background: transparent; color: #64748b;">Params</button>
                    <button type="button" onclick="switchRequestTab('code')" id="tabBtn_code" class="req-tab-btn" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; background: transparent; color: #64748b;">Client Code (Next.js &amp; Flutter)</button>
                </div>

                <!-- Tab 1: Headers -->
                <div id="reqTab_headers" class="req-tab-content">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: grid; grid-template-columns: 200px 1fr 40px; gap: 8px; font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase;">
                            <span>Header Key</span>
                            <span>Header Value</span>
                            <span></span>
                        </div>
                        <div id="headersTableRows" style="display: flex; flex-direction: column; gap: 6px;">
                            <!-- Populated dynamically via JS -->
                        </div>
                        <button type="button" onclick="addHeaderRow('', '')" style="align-self: flex-start; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 0.75rem; font-weight: 700; color: #475569; cursor: pointer; margin-top: 4px;">
                            + Add Custom Header
                        </button>
                    </div>
                </div>

                <!-- Tab 2: Body -->
                <div id="reqTab_body" class="req-tab-content" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #64748b;">JSON Raw Payload</span>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" onclick="beautifyJsonBody()" style="padding: 3px 8px; font-size: 0.72rem; font-weight: 600; border-radius: 5px; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer;">Beautify</button>
                            <button type="button" onclick="clearJsonBody()" style="padding: 3px 8px; font-size: 0.72rem; font-weight: 600; border-radius: 5px; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer;">Clear</button>
                        </div>
                    </div>
                    <textarea id="requestBodyTextarea" rows="8" placeholder="{}" style="width: 100%; padding: 12px; font-family: 'Consolas', monospace; font-size: 0.85rem; color: #0f172a; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; box-sizing: border-box; resize: vertical; line-height: 1.4;"></textarea>
                </div>

                <!-- Tab 3: Params -->
                <div id="reqTab_params" class="req-tab-content" style="display: none;">
                    <div id="paramsTableRows" style="display: flex; flex-direction: column; gap: 6px;">
                        <!-- Dynamic Query params -->
                    </div>
                    <button type="button" onclick="addParamRow('', '')" style="align-self: flex-start; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 0.75rem; font-weight: 700; color: #475569; cursor: pointer; margin-top: 6px;">
                        + Add Query Param
                    </button>
                </div>

                <!-- Tab 4: Client Code Generator -->
                <div id="reqTab_code" class="req-tab-content" style="display: none;">
                    <div style="display: flex; gap: 6px; margin-bottom: 8px;">
                        <button type="button" onclick="showSnippet('nextjs')" id="snipTab_nextjs" style="padding: 4px 10px; font-size: 0.75rem; font-weight: 700; border-radius: 5px; border: 1px solid #cbd5e1; background: #1e293b; color: #ffffff; cursor: pointer;">Next.js / TypeScript</button>
                        <button type="button" onclick="showSnippet('flutter')" id="snipTab_flutter" style="padding: 4px 10px; font-size: 0.75rem; font-weight: 700; border-radius: 5px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; cursor: pointer;">Flutter / Dart (Dio)</button>
                        <button type="button" onclick="showSnippet('curl')" id="snipTab_curl" style="padding: 4px 10px; font-size: 0.75rem; font-weight: 700; border-radius: 5px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; cursor: pointer;">cURL</button>
                        <button type="button" onclick="copySnippetCode()" style="margin-left: auto; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; border-radius: 5px; border: 1px solid #10b981; background: #ecfdf5; color: #047857; cursor: pointer;">Copy Code</button>
                    </div>
                    <pre id="snippetCodeDisplay" style="background: #0f172a; color: #e2e8f0; padding: 14px; border-radius: 8px; font-size: 0.8rem; font-family: 'Consolas', monospace; max-height: 200px; overflow: auto; margin: 0; line-height: 1.45;"></pre>
                </div>

            </div>
        </div>

        <!-- Live Response Inspector Card -->
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.03); padding: 18px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 0.85rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.04em;">Response Console</span>
                    <span id="responseStatusBadge" style="display: none; padding: 3px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 800;"></span>
                </div>
                <div style="display: flex; align-items: center; gap: 14px; font-size: 0.78rem; font-weight: 600; color: #64748b;">
                    <span id="responseTimeDisplay">Time: -- ms</span>
                    <span id="responseSizeDisplay">Size: -- KB</span>
                    <button type="button" onclick="copyResponseBody()" style="padding: 4px 10px; font-size: 0.75rem; font-weight: 700; border-radius: 6px; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy JSON
                    </button>
                </div>
            </div>

            <!-- Response Tabs -->
            <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                <button type="button" onclick="switchResponseTab('body')" id="respTabBtn_body" style="padding: 4px 12px; font-size: 0.75rem; font-weight: 700; border-radius: 5px; border: none; background: #e0f2fe; color: #0369a1; cursor: pointer;">Response Body</button>
                <button type="button" onclick="switchResponseTab('headers')" id="respTabBtn_headers" style="padding: 4px 12px; font-size: 0.75rem; font-weight: 700; border-radius: 5px; border: none; background: transparent; color: #64748b; cursor: pointer;">Headers</button>
            </div>

            <!-- Response Body Display -->
            <div id="respContainer_body" style="background: #090d16; border-radius: 10px; padding: 14px; min-height: 240px; max-height: 480px; overflow: auto; border: 1px solid #1e293b;">
                <pre id="responseJsonOutput" style="margin: 0; font-family: 'Consolas', monospace; font-size: 0.82rem; color: #38bdf8; line-height: 1.5; white-space: pre-wrap; word-break: break-all;">Click "Send" to execute the request and view live JSON output.</pre>
            </div>

            <!-- Response Headers Display -->
            <div id="respContainer_headers" style="display: none; background: #090d16; border-radius: 10px; padding: 14px; min-height: 240px; max-height: 480px; overflow: auto; border: 1px solid #1e293b;">
                <pre id="responseHeadersOutput" style="margin: 0; font-family: 'Consolas', monospace; font-size: 0.82rem; color: #94a3b8; line-height: 1.5; white-space: pre-wrap;"></pre>
            </div>
        </div>
    </div>
</div>

<style>
    /* Postman Method Colors */
    .method-get { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .method-post { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .method-put { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .method-patch { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .method-delete { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

    .endpoint-nav-item:hover {
        background: #f1f5f9;
    }
    .endpoint-nav-item.active {
        background: #eff6ff;
        border-color: #93c5fd;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    /* JSON Syntax Colors */
    .json-key { color: #38bdf8; font-weight: 600; }
    .json-string { color: #4ade80; }
    .json-number { color: #f59e0b; }
    .json-boolean { color: #ec4899; font-weight: 700; }
    .json-null { color: #a855f7; font-style: italic; }
</style>

<script>
    // State storage
    let currentEndpoint = null;
    let activeToken = '';
    let currentHeaders = [];
    let currentParams = [];
    let currentSnippetLang = 'nextjs';

    document.addEventListener('DOMContentLoaded', function () {
        // Automatically fetch initial Super Admin Bearer Token
        fetchPersonaToken('super_admin');

        // Select the first endpoint automatically
        const firstCategory = @json($categories[0] ?? null);
        if (firstCategory && firstCategory.endpoints.length > 0) {
            selectEndpoint(firstCategory.endpoints[0]);
        }
    });

    // Switch Persona and generate corresponding token
    function switchPersona(role) {
        if (role === 'none') {
            activeToken = '';
            document.getElementById('activePersonaBadge').innerText = 'Public (Guest)';
            document.getElementById('activePersonaBadge').style.background = '#f1f5f9';
            document.getElementById('activePersonaBadge').style.color = '#475569';
            updateAuthHeader();
            if (window.Toast) window.Toast.info("Switched to Unauthenticated mode.");
            return;
        }

        fetchPersonaToken(role);
    }

    function fetchPersonaToken(role) {
        fetch("{{ route('admin.api_tester.token') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ role: role })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                activeToken = data.token;
                document.getElementById('activePersonaBadge').innerText = data.user.role.toUpperCase().replace('_', ' ');
                updateAuthHeader();
                if (window.Toast) window.Toast.success(`Bearer token active for persona: ${data.user.full_name}`);
                updateClientSnippets();
            }
        })
        .catch(err => {
            console.error(err);
            if (window.Toast) window.Toast.error("Failed to generate persona token.");
        });
    }

    function updateAuthHeader() {
        const authRowIndex = currentHeaders.findIndex(h => h.key.toLowerCase() === 'authorization');
        if (activeToken) {
            if (authRowIndex >= 0) {
                currentHeaders[authRowIndex].val = `Bearer ${activeToken}`;
            } else {
                currentHeaders.push({ key: 'Authorization', val: `Bearer ${activeToken}` });
            }
        } else {
            if (authRowIndex >= 0) {
                currentHeaders.splice(authRowIndex, 1);
            }
        }
        renderHeadersTable();
        updateClientSnippets();
    }

    // Select an endpoint from collection
    function selectEndpoint(endpoint) {
        currentEndpoint = endpoint;

        // Highlight active nav item
        document.querySelectorAll('.endpoint-nav-item').forEach(el => el.classList.remove('active'));
        const navEl = document.getElementById('nav_item_' + endpoint.id);
        if (navEl) navEl.classList.add('active');

        // Update Title, Path, Method
        document.getElementById('currentEndpointTitle').innerText = endpoint.name;
        document.getElementById('currentEndpointDesc').innerText = endpoint.description || '';
        document.getElementById('requestMethodSelect').value = endpoint.method;
        document.getElementById('requestUrlInput').value = endpoint.path;

        // Auth requirement badge
        const authBadge = document.getElementById('authRequirementBadge');
        if (endpoint.requires_auth) {
            authBadge.innerText = endpoint.required_permission ? `Required: ${endpoint.required_permission}` : 'Auth: Bearer Token';
            authBadge.style.background = '#fef2f2';
            authBadge.style.color = '#b91c1c';
            authBadge.style.borderColor = '#fecaca';
        } else {
            authBadge.innerText = 'Public Access';
            authBadge.style.background = '#ecfdf5';
            authBadge.style.color = '#047857';
            authBadge.style.borderColor = '#a7f3d0';
        }

        // Setup Headers
        currentHeaders = [];
        if (endpoint.headers) {
            Object.keys(endpoint.headers).forEach(k => {
                let v = endpoint.headers[k];
                if (v.includes('{token}')) {
                    v = activeToken ? `Bearer ${activeToken}` : '';
                }
                currentHeaders.push({ key: k, val: v });
            });
        }
        if (activeToken && !currentHeaders.some(h => h.key.toLowerCase() === 'authorization')) {
            currentHeaders.push({ key: 'Authorization', val: `Bearer ${activeToken}` });
        }
        renderHeadersTable();

        // Setup Query Params
        currentParams = [];
        if (endpoint.params) {
            Object.keys(endpoint.params).forEach(k => {
                currentParams.push({ key: k, val: endpoint.params[k] });
            });
        }
        renderParamsTable();

        // Setup Body
        document.getElementById('requestBodyTextarea').value = endpoint.body || '';

        // Switch to Body tab if POST/PATCH/PUT and has body
        if (['POST', 'PUT', 'PATCH'].includes(endpoint.method) && endpoint.body) {
            switchRequestTab('body');
        } else {
            switchRequestTab('headers');
        }

        updateClientSnippets();
    }

    // Render Headers Rows
    function renderHeadersTable() {
        const container = document.getElementById('headersTableRows');
        container.innerHTML = '';
        currentHeaders.forEach((h, idx) => {
            const row = document.createElement('div');
            row.style = "display: grid; grid-template-columns: 200px 1fr 40px; gap: 8px; align-items: center;";
            row.innerHTML = `
                <input type="text" value="${escapeHtml(h.key)}" oninput="updateHeaderKey(${idx}, this.value)" placeholder="Header" style="padding: 6px 10px; font-size: 0.8rem; font-family: monospace; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                <input type="text" value="${escapeHtml(h.val)}" oninput="updateHeaderVal(${idx}, this.value)" placeholder="Value" style="padding: 6px 10px; font-size: 0.8rem; font-family: monospace; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                <button type="button" onclick="removeHeaderRow(${idx})" style="background: transparent; border: none; color: #ef4444; font-size: 1rem; cursor: pointer; text-align: center;">✕</button>
            `;
            container.appendChild(row);
        });
        document.getElementById('headerCount').innerText = currentHeaders.length;
    }

    function addHeaderRow(key, val) {
        currentHeaders.push({ key: key, val: val });
        renderHeadersTable();
        updateClientSnippets();
    }

    function removeHeaderRow(idx) {
        currentHeaders.splice(idx, 1);
        renderHeadersTable();
        updateClientSnippets();
    }

    function updateHeaderKey(idx, key) { currentHeaders[idx].key = key; updateClientSnippets(); }
    function updateHeaderVal(idx, val) { currentHeaders[idx].val = val; updateClientSnippets(); }

    // Render Query Params Rows
    function renderParamsTable() {
        const container = document.getElementById('paramsTableRows');
        container.innerHTML = '';
        if (currentParams.length === 0) {
            container.innerHTML = '<span style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">No query parameters configured for this endpoint.</span>';
            return;
        }
        currentParams.forEach((p, idx) => {
            const row = document.createElement('div');
            row.style = "display: grid; grid-template-columns: 180px 1fr 40px; gap: 8px; align-items: center;";
            row.innerHTML = `
                <input type="text" value="${escapeHtml(p.key)}" oninput="updateParamKey(${idx}, this.value)" placeholder="Param Name" style="padding: 6px 10px; font-size: 0.8rem; font-family: monospace; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                <input type="text" value="${escapeHtml(p.val)}" oninput="updateParamVal(${idx}, this.value)" placeholder="Param Value" style="padding: 6px 10px; font-size: 0.8rem; font-family: monospace; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                <button type="button" onclick="removeParamRow(${idx})" style="background: transparent; border: none; color: #ef4444; font-size: 1rem; cursor: pointer; text-align: center;">✕</button>
            `;
            container.appendChild(row);
        });
    }

    function addParamRow(key, val) {
        currentParams.push({ key: key, val: val });
        renderParamsTable();
        updateClientSnippets();
    }

    function removeParamRow(idx) {
        currentParams.splice(idx, 1);
        renderParamsTable();
        updateClientSnippets();
    }

    function updateParamKey(idx, key) { currentParams[idx].key = key; updateClientSnippets(); }
    function updateParamVal(idx, val) { currentParams[idx].val = val; updateClientSnippets(); }

    // Beautify & Clear JSON Body
    function beautifyJsonBody() {
        const textarea = document.getElementById('requestBodyTextarea');
        try {
            const parsed = JSON.parse(textarea.value);
            textarea.value = JSON.stringify(parsed, null, 2);
            if (window.Toast) window.Toast.success("JSON formatted successfully.");
        } catch (e) {
            if (window.Toast) window.Toast.error("Invalid JSON syntax in request body.");
        }
    }

    function clearJsonBody() {
        document.getElementById('requestBodyTextarea').value = '';
    }

    // Tabs Switcher
    function switchRequestTab(tab) {
        ['headers', 'body', 'params', 'code'].forEach(t => {
            document.getElementById('reqTab_' + t).style.display = (t === tab) ? 'block' : 'none';
            const btn = document.getElementById('tabBtn_' + t);
            if (t === tab) {
                btn.style.background = '#dbeafe';
                btn.style.color = '#1e40af';
            } else {
                btn.style.background = 'transparent';
                btn.style.color = '#64748b';
            }
        });
    }

    function switchResponseTab(tab) {
        document.getElementById('respContainer_body').style.display = (tab === 'body') ? 'block' : 'none';
        document.getElementById('respContainer_headers').style.display = (tab === 'headers') ? 'block' : 'none';
        document.getElementById('respTabBtn_body').style.background = (tab === 'body') ? '#e0f2fe' : 'transparent';
        document.getElementById('respTabBtn_body').style.color = (tab === 'body') ? '#0369a1' : '#64748b';
        document.getElementById('respTabBtn_headers').style.background = (tab === 'headers') ? '#e0f2fe' : 'transparent';
        document.getElementById('respTabBtn_headers').style.color = (tab === 'headers') ? '#0369a1' : '#64748b';
    }

    // Live Execution via fetch()
    async function executeApiCall() {
        const method = document.getElementById('requestMethodSelect').value.toUpperCase();
        let path = document.getElementById('requestUrlInput').value.trim();
        const sendBtn = document.getElementById('sendRequestBtn');

        // Build Query String
        if (currentParams.length > 0) {
            const queryParams = new URLSearchParams();
            currentParams.forEach(p => {
                if (p.key.trim()) queryParams.append(p.key.trim(), p.val.trim());
            });
            const qs = queryParams.toString();
            if (qs) {
                path += (path.includes('?') ? '&' : '?') + qs;
            }
        }

        // Build Headers
        const headers = {};
        currentHeaders.forEach(h => {
            if (h.key.trim()) headers[h.key.trim()] = h.val.trim();
        });

        // Build Body
        let body = null;
        if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
            const rawBody = document.getElementById('requestBodyTextarea').value.trim();
            if (rawBody) {
                try {
                    JSON.parse(rawBody); // Validate JSON
                    body = rawBody;
                    if (!headers['Content-Type']) headers['Content-Type'] = 'application/json';
                } catch (e) {
                    if (window.Toast) window.Toast.error("Request body contains invalid JSON.");
                    return;
                }
            }
        }

        // UI Loading state
        sendBtn.disabled = true;
        sendBtn.innerHTML = `
            <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="1"/></svg>
            <span>Sending...</span>
        `;
        document.getElementById('responseJsonOutput').innerHTML = '<span style="color: #64748b;">Awaiting server response...</span>';

        const startTime = performance.now();

        try {
            const response = await fetch(path, {
                method: method,
                headers: headers,
                body: body
            });

            const endTime = performance.now();
            const latency = Math.round(endTime - startTime);

            // Read text & calculate size
            const rawText = await response.text();
            const sizeKb = (new Blob([rawText]).size / 1024).toFixed(2);

            // Update Latency & Size
            document.getElementById('responseTimeDisplay').innerText = `Time: ${latency} ms`;
            document.getElementById('responseSizeDisplay').innerText = `Size: ${sizeKb} KB`;

            // Status Badge
            const statusBadge = document.getElementById('responseStatusBadge');
            statusBadge.style.display = 'inline-block';
            statusBadge.innerText = `${response.status} ${response.statusText || getStatusText(response.status)}`;
            if (response.status >= 200 && response.status < 300) {
                statusBadge.style.background = '#dcfce7';
                statusBadge.style.color = '#15803d';
                if (window.Toast) window.Toast.success(`HTTP ${response.status}: Request successful in ${latency}ms`);
            } else if (response.status === 401 || response.status === 403) {
                statusBadge.style.background = '#fee2e2';
                statusBadge.style.color = '#b91c1c';
                if (window.Toast) window.Toast.warning(`HTTP ${response.status}: Authentication or Authorization failed.`);
            } else if (response.status === 422) {
                statusBadge.style.background = '#fef3c7';
                statusBadge.style.color = '#b45309';
                if (window.Toast) window.Toast.warning(`HTTP 422: Validation error.`);
            } else {
                statusBadge.style.background = '#fee2e2';
                statusBadge.style.color = '#b91c1c';
                if (window.Toast) window.Toast.error(`HTTP ${response.status}: Server returned an error.`);
            }

            // Parse JSON with syntax highlighting
            try {
                const jsonObj = JSON.parse(rawText);
                document.getElementById('responseJsonOutput').innerHTML = syntaxHighlight(jsonObj);
            } catch (e) {
                document.getElementById('responseJsonOutput').innerText = rawText || '(Empty response)';
            }

            // Headers Viewer
            let headersText = '';
            response.headers.forEach((val, key) => {
                headersText += `${key}: ${val}\n`;
            });
            document.getElementById('responseHeadersOutput').innerText = headersText || '(No response headers)';

        } catch (error) {
            const endTime = performance.now();
            document.getElementById('responseTimeDisplay').innerText = `Time: ${Math.round(endTime - startTime)} ms`;
            document.getElementById('responseStatusBadge').style.display = 'inline-block';
            document.getElementById('responseStatusBadge').innerText = 'Network Error / Failed';
            document.getElementById('responseStatusBadge').style.background = '#fee2e2';
            document.getElementById('responseStatusBadge').style.color = '#b91c1c';
            document.getElementById('responseJsonOutput').innerText = `Error connecting to ${path}:\n${error.message}`;
            if (window.Toast) window.Toast.error("Network or connection error during API request.");
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                <span>Send</span>
            `;
        }
    }

    // Code Generator Snippets
    function showSnippet(lang) {
        currentSnippetLang = lang;
        ['nextjs', 'flutter', 'curl'].forEach(l => {
            const btn = document.getElementById('snipTab_' + l);
            if (l === lang) {
                btn.style.background = '#1e293b';
                btn.style.color = '#ffffff';
            } else {
                btn.style.background = '#ffffff';
                btn.style.color = '#475569';
            }
        });
        updateClientSnippets();
    }

    function updateClientSnippets() {
        const method = document.getElementById('requestMethodSelect').value.toUpperCase();
        const path = document.getElementById('requestUrlInput').value.trim();
        const body = document.getElementById('requestBodyTextarea').value.trim();

        let code = '';
        if (currentSnippetLang === 'nextjs') {
            code = `// Next.js (TypeScript) / TanStack Query API Client Call
import { apiClient } from '@/lib/api';

export async function executeCall() {
  const response = await fetch('${path}', {
    method: '${method}',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ${activeToken || '<SANCTUM_TOKEN>'}',
    },${body ? `\n    body: JSON.stringify(${body.replace(/\n/g, '\n    ')}),` : ''}
  });

  if (!response.ok) {
    throw new Error(\`API error: \${response.status}\`);
  }

  return response.json();
}`;
        } else if (currentSnippetLang === 'flutter') {
            code = `// Flutter (Dart) Dio API Client Call
import 'package:dio/dio.dart';

Future<Response> executeCall(Dio dio) async {
  return await dio.request(
    '${path}',
    options: Options(
      method: '${method}',
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ${activeToken || '<SANCTUM_TOKEN>'}',
      },
    ),${body ? `\n    data: ${body},` : ''}
  );
}`;
        } else if (currentSnippetLang === 'curl') {
            code = `curl -X ${method} "${window.location.origin}${path}" \\
  -H "Accept: application/json" \\
  -H "Content-Type: application/json" \\
  -H "Authorization: Bearer ${activeToken || '<SANCTUM_TOKEN>'}"${body ? ` \\\n  -d '${body.replace(/'/g, "'\\''")}'` : ''}`;
        }

        document.getElementById('snippetCodeDisplay').innerText = code;
    }

    function copySnippetCode() {
        const text = document.getElementById('snippetCodeDisplay').innerText;
        navigator.clipboard.writeText(text).then(() => {
            if (window.Toast) window.Toast.success("Client code snippet copied to clipboard.");
        });
    }

    function copyResponseBody() {
        const text = document.getElementById('responseJsonOutput').innerText;
        navigator.clipboard.writeText(text).then(() => {
            if (window.Toast) window.Toast.success("JSON response copied to clipboard.");
        });
    }

    // Filter Endpoints in Collection Tree
    function filterEndpoints(query) {
        query = query.toLowerCase().trim();
        document.querySelectorAll('.category-block').forEach(catEl => {
            let visibleCount = 0;
            catEl.querySelectorAll('.endpoint-nav-item').forEach(itemEl => {
                const name = itemEl.getAttribute('data-name').toLowerCase();
                const path = itemEl.getAttribute('data-path').toLowerCase();
                const method = itemEl.getAttribute('data-method').toLowerCase();

                if (!query || name.includes(query) || path.includes(query) || method.includes(query)) {
                    itemEl.style.display = 'flex';
                    visibleCount++;
                } else {
                    itemEl.style.display = 'none';
                }
            });

            catEl.style.display = (visibleCount > 0 || !query) ? 'block' : 'none';
        });
    }

    function toggleCategory(catId) {
        const el = document.getElementById('cat_items_' + catId);
        if (el) {
            el.style.display = (el.style.display === 'none') ? 'flex' : 'none';
        }
    }

    function resetConsole() {
        document.getElementById('responseStatusBadge').style.display = 'none';
        document.getElementById('responseTimeDisplay').innerText = 'Time: -- ms';
        document.getElementById('responseSizeDisplay').innerText = 'Size: -- KB';
        document.getElementById('responseJsonOutput').innerHTML = '<span style="color: #64748b;">Console reset. Click "Send" to execute a fresh request.</span>';
        if (window.Toast) window.Toast.info("Console reset.");
    }

    // JSON Syntax Highlighting
    function syntaxHighlight(json) {
        if (typeof json != 'string') {
            json = JSON.stringify(json, undefined, 2);
        }
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            var cls = 'json-number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'json-key';
                } else {
                    cls = 'json-string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'json-boolean';
            } else if (/null/.test(match)) {
                cls = 'json-null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function getStatusText(code) {
        const map = {
            200: 'OK', 201: 'Created', 204: 'No Content',
            400: 'Bad Request', 401: 'Unauthorized', 403: 'Forbidden',
            404: 'Not Found', 422: 'Unprocessable Content', 429: 'Too Many Requests',
            500: 'Internal Server Error'
        };
        return map[code] || 'Status';
    }
</script>
@endsection

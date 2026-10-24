<!-- Standardized SchoolMeal Sidebar Brand / Header (Identical to Super Admin) -->
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
    <button type="button" onclick="toggleSidebarCollapse()" class="sidebar-close-btn" style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer; display: none;" aria-label="Close Sidebar">✕</button>
</div>

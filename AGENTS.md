# School Meal E-Commerce System Guidelines

## UI & Notification Standards: Toast Messages Only
- **Strict Rule**: In this project and system, **ALL messages (success, error, warning, status, session flash, validation errors, and user feedback) MUST use floating Toast notifications**.
- **Prohibition**: **DO NOT use inline alert banners, callouts, error lists, or static message boxes on layouts, page headers, cards, or inside forms**.
- **Implementation**:
  - Global Blade component: `<x-toast-container />` included in all master layouts (`master_super_admin.blade.php`, `admin.blade.php`, `login.blade.php`).
  - Automatic session detection: Server-side `session('status')`, `session('success')`, `session('error')`, `session('warning')`, `session('info')`, and `$errors->all()` are converted to floating toasts on page load.
  - Client-side JavaScript API: Use `window.Toast.success(msg)`, `window.Toast.error(msg)`, `window.Toast.warning(msg)`, `window.Toast.info(msg)`, or `window.showToast(msg, type)`.

## Data Tables & List Standard: Meta Content List Concept
- **Sticky Frozen Left Columns**:
  1. Checkbox column (`sticky-col-select`, 44px)
  2. Entity ID column (`sticky-col-code`, 95px)
  3. Primary entity column (`sticky-col-student` / `sticky-col-primary`, 310px) with subtle right border (`2px solid #CBD5E1`) and drop-shadow (`box-shadow: 4px 0 10px -2px rgba(15, 23, 42, 0.08)`).
- **Inline Context Menu Action (`[...]`)**:
  - The Action button MUST be rendered as an inline context menu button (`.btn-context-menu`) directly beside the entity name inside the frozen cell (matching the Meta Content List concept).
  - Clicking `[...]` displays a floating context popover (`position: fixed; z-index: 9999`) with actions (View, Edit, Copy Data, Functionals, Archive).
- **Horizontal Scroll for Attribute Columns**:
  - All other attribute columns scroll smoothly horizontally beneath the frozen columns.
  - Table element must use `border-collapse: separate; border-spacing: 0; white-space: nowrap;` for clean border rendering during scroll.
- **Dynamic Column Customizer ("Columns X/Y")**:
  - Table topbar must provide a "Columns" checklist dropdown with a counter badge (e.g. `7/12`).
  - Column visibility is toggled instantaneously with `<style id="dynamicColumnStyles">` and persisted in `localStorage`.
  - See full specifications in [META_CONTENT_LIST_STANDARD.md](file:///e:/All%20Hostings/School%20Meal%20E-Commerce/sme_backend/docs/STANDARDS/META_CONTENT_LIST_STANDARD.md).


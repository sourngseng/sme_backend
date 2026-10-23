# UI Feedback Standard: Toast Messages Only

## Critical Rule
In this project and system:
**ALL messages (success, error, warning, status, session flash, validation errors, and user feedback) MUST use floating Toast notifications.**
**DO NOT use inline alert banners, callouts, error lists, or static message boxes on layouts, page headers, cards, or inside forms.**

### Guidelines
1. **Zero Layout Shift**: Inline alert messages distort form geometry, push down buttons, and break zero-scroll designs. All notifications must float unobtrusively in a fixed Toast container (top-right corner).
2. **Global Component**: Use the `<x-toast-container />` Blade component in layouts (`master_super_admin.blade.php`, `login.blade.php`, etc.).
3. **Session Flashes**: Handle `session('status')`, `session('success')`, `session('error')`, `session('warning')`, `session('info')`, and `$errors->all()` via `Toast.show(...)` rather than rendering inline HTML blocks.
4. **Client-side Actions**: For JavaScript interactions (e.g. copying links, autofilling demo credentials, AJAX submissions, form validation), invoke `window.Toast.success(...)`, `window.Toast.error(...)`, or `window.showToast(...)`.

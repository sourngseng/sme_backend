# School Meal E-Commerce System Guidelines

## UI & Notification Standards: Toast Messages Only
- **Strict Rule**: In this project and system, **ALL messages (success, error, warning, status, session flash, validation errors, and user feedback) MUST use floating Toast notifications**.
- **Prohibition**: **DO NOT use inline alert banners, callouts, error lists, or static message boxes on layouts, page headers, cards, or inside forms**.
- **Implementation**:
  - Global Blade component: `<x-toast-container />` included in all master layouts (`master_super_admin.blade.php`, `admin.blade.php`, `login.blade.php`).
  - Automatic session detection: Server-side `session('status')`, `session('success')`, `session('error')`, `session('warning')`, `session('info')`, and `$errors->all()` are converted to floating toasts on page load.
  - Client-side JavaScript API: Use `window.Toast.success(msg)`, `window.Toast.error(msg)`, `window.Toast.warning(msg)`, `window.Toast.info(msg)`, or `window.showToast(msg, type)`.

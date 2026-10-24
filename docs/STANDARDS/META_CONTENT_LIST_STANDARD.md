# Meta Content List Standard: Sticky Frozen Entity & Dynamic Customizer

This document defines the official design and architectural standard for all tabular listings across the School Meal E-Commerce platform (Admin, School Admin, Merchant, Delivery, Finance, etc.).

## 1. Architectural Philosophy
Inspired by Meta Business Suite / Meta Content List, our table lists provide rapid access to primary entity identity and actions while allowing unlimited horizontal data discovery through smooth scrolling and on-demand column customization.

---

## 2. Core Layout Principles

### A. Three Leftmost Columns Are Pinned (Sticky / Frozen)
1. **Checkbox Column (`sticky-col-select`)**:
   - `left: 0; width: 44px; min-width: 44px; text-align: center;`
   - Selects / bulk checks rows.
2. **Entity Code / ID Column (`sticky-col-code`)**:
   - `left: 44px; width: 95px; min-width: 95px;`
   - Clicking opens profile or detail modal.
3. **Primary Entity + Inline Context Action (`sticky-col-student` / `sticky-col-primary`)**:
   - `left: 139px; min-width: 310px; width: 310px;`
   - Contains:
     - Avatar / Photo / Thumbnail
     - Primary Name & Secondary Translation (e.g., Latin name + Khmer name)
     - **Context Menu Action Button (`.btn-context-menu`)**: Placed directly after the entity name on the right edge of this frozen cell.
   - **Boundary Divider & Shadow**:
     - `border-right: 2px solid #CBD5E1 !important;`
     - `box-shadow: 4px 0 10px -2px rgba(15, 23, 42, 0.08);`

### B. Smooth Horizontal Scrolling for Secondary Columns
- Table container uses `.table-responsive` with `overflow-x: auto;`.
- Table element uses `border-collapse: separate; border-spacing: 0; white-space: nowrap;` so that sticky borders remain sharp and never flicker or disconnect during scroll.
- All other attribute columns (Gender, Grade, Parent, Phone, Email, Meal Plan, Allergies, Status, Timestamps, etc.) scroll smoothly beneath the frozen columns.

---

## 3. Inline Context Menu (`[...]`)
- **Visual Style**: Clean, modern 32x30px pill-bordered button containing an icon with 3 horizontal dots (`...`).
- **Placement**: Directly inside the primary frozen cell alongside the name (never as a detached right-side column).
- **Floating Popover**:
  - Attached with `position: fixed; z-index: 9999;` to prevent any clipping from `overflow-x: auto` containers.
  - Dynamically positioned using `.getBoundingClientRect()` of the clicked trigger button with smart viewport boundary detection (opens upward if near bottom edge; aligns left if near right edge).
  - Automatically dismisses when clicking outside or scrolling the page/table.
  - Standard action suite:
    - View Full Profile / Details
    - Edit Record
    - Copy Key Data (e.g. Phone, Code) to Clipboard with Toast feedback
    - Primary Functional Action (Call, Print QR badge, etc.)
    - Archive / Deactivate Record (with red danger styling)

---

## 4. Meta Column Customizer (`Columns X/Y`)
- **Trigger**: "Columns (X/Y)" pill button in `.table-topbar` next to "Show entries".
- **Counter Badge**: Real-time ratio indicator (e.g., `7/12`) displaying visible scrollable columns.
- **Dropdown Checklist**:
  - Pinned frozen columns are permanent and omitted from the toggle list.
  - All scrollable columns can be checked/unchecked by the user.
  - Includes a "Reset" link to quickly restore standard default visibility.
- **Zero-Latency Injection via Dynamic Stylesheet**:
  - Instead of slow DOM node queries or rebuilding HTML, column visibility is controlled by generating a `<style id="dynamicColumnStyles">` tag containing:
    ```css
    [data-col="phone"] { display: none !important; }
    [data-col="email"] { display: none !important; }
    ```
  - Result: **0ms instant switching** that applies automatically to both existing rows and newly loaded AJAX/pagination rows!
- **State Persistence**:
  - Preferences saved automatically in browser `localStorage` (e.g., `sme_students_columns_v1`).

---

## 5. UI Notification Rule (Strict Adherence)
- Strictly comply with [AGENTS.md](file:///e:/All%20Hostings/School%20Meal%20E-Commerce/sme_backend/AGENTS.md):
  - **TOAST ONLY**: Use `window.Toast.success(msg)`, `window.Toast.error(msg)`, `window.Toast.info(msg)`, `window.Toast.warning(msg)`.
  - **NO INLINE BANNERS**: Never render inline alerts, red error lists, or static banners inside table cards or form pages.

---

## 6. Checklist When Applying to New Lists
When implementing this concept on orders, meals, menus, drivers, or inventory:
- [ ] Assign `sticky-col-select`, `sticky-col-code`, `sticky-col-primary` to the first 3 columns.
- [ ] Add `.btn-context-menu` inside `sticky-col-primary` right after the main name/title.
- [ ] Add `data-col="..."` attributes to both `<th>` and `<td>` for all columns.
- [ ] Add `COLUMNS_SCHEMA` configuration and `#columnCustomizerPopover` in the table topbar.
- [ ] Use `border-collapse: separate; border-spacing: 0;` on the `<table>`.
- [ ] Test in light/dark/responsive viewports to ensure sticky shadow and z-indexes (`10`, `12`, `30`, `9999`) align correctly.

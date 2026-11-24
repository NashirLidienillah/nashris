# UI/UX & Design System Guidelines — NasHRIS

## 1. Brand & Layout Architecture (Desktop-First Responsive)
- **App Identity:** NasHRIS logo in sidebar header with clean geometric icon + bold typography.
- **Desktop (1024px and above):** Fixed left sidebar (`w-64` / `256px`), top header bar (`h-16`) showing page title, breadcrumb, and logged-in user badge, and spacious fluid main content area (`bg-slate-50`). Data tables must breathe horizontally without cramped columns.
- **Mobile (below 1024px):** Sidebar collapses into an Alpine.js slide-over drawer. On Employee dashboard, the **Clock-In / Clock-Out widget** must appear first at the top.

## 2. Color Palette & Typography
- **Font:** `Plus Jakarta Sans` or `Inter` via Google Fonts.
- **Primary Brand:** Indigo (`indigo-600` primary buttons/active nav, `indigo-700` hover, `indigo-50` active sidebar background).
- **Neutral Surfaces:** `slate-900` (primary headings), `slate-600` (body text), `slate-200` (card/table borders), `white` (cards/modals).
- **Semantic Status Badges (`rounded-full px-2.5 py-0.5 text-xs font-medium`):**
  - `Present` / `Approved` / `Paid` / `PKWTT`: `bg-emerald-50 text-emerald-700 border border-emerald-200`
  - `Late` / `Pending Manager` / `Pending HR` / `Draft` / `PKWT`: `bg-amber-50 text-amber-700 border border-amber-200`
  - `Absent` / `Rejected`: `bg-rose-50 text-rose-700 border border-rose-200`
  - `On Leave` / `Probation`: `bg-sky-50 text-sky-700 border border-sky-200`

## 3. Core UI Components (`resources/views/components/`)
- Build reusable Blade components: `<x-stat-card>`, `<x-status-badge>`, `<x-data-table>`, `<x-modal>`, and `<x-empty-state>`.
- Format all currency values in Indonesian Rupiah (`Rp 8.500.000`) and dates in readable format (`23 Nov 2026`).
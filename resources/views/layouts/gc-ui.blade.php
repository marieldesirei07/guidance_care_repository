{{--
    GuidanceCare shared UI rules (single source of truth for layout, spacing, tables, badges, buttons).

    Why this is a plain <style> block and not Tailwind classes:
    Tailwind only compiles classes it found in the views at the last `npm run build`.
    Classes added later (e.g. flex-wrap, py-8) silently do nothing until a rebuild.
    Everything below works immediately, with no build step.

    ISO 9241-110 / HCI notes:
    - Consistency: every portal uses the same container, rhythm, table, badge and button rules.
    - Self-descriptiveness: status is always a labelled pill, never colour alone.
    - Error tolerance: buttons wrap instead of overlapping; tables scroll sideways instead of breaking.
--}}
<style>
    /* ---- Brand tokens (one place) ---- */
    :root {
        --gc-primary: #0038A8;        /* STI blue: primary buttons, links, eyebrows */
        --gc-primary-hover: #002c85;
        --gc-accent: #FCD116;         /* STI yellow: highlights only, never for reject actions */
        --gc-danger: #dc2626;
        --gc-danger-hover: #b91c1c;
    }

    /* ---- Page structure (margins) ---- */
    .gc-page { padding: 32px 0 48px; }
    .gc-container { max-width: 80rem; margin: 0 auto; padding: 0 16px; box-sizing: border-box; }
    @media (min-width: 640px)  { .gc-container { padding: 0 24px; } }
    @media (min-width: 1024px) { .gc-container { padding: 0 32px; } }

    /* Vertical rhythm: one gap between header, stats, and each table card */
    .gc-stack { display: flex; flex-direction: column; gap: 24px; }

    /* ---- Cards ---- */
    .gc-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
    .gc-card-body { padding: 24px; }
    .gc-card-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px 16px; padding: 16px 24px; border-bottom: 1px solid #f3f4f6; }
    .gc-card-title { margin: 0; font-size: 16px; font-weight: 600; color: #111827; }
    .gc-link-all { font-size: 13px; font-weight: 600; color: var(--gc-primary); text-decoration: none; white-space: nowrap; }
    .gc-link-all:hover { color: #1e40af; text-decoration: underline; }

    /* ---- Page header (hero) ---- */
    .gc-hero { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    .gc-hero-text { min-width: 0; flex: 1 1 280px; }
    .gc-eyebrow { margin: 0 0 6px; font-size: 11px; font-weight: 600; color: var(--gc-primary); text-transform: uppercase; letter-spacing: .05em; }
    .gc-title { margin: 0 0 4px; font-size: 24px; font-weight: 700; color: #111827; line-height: 1.25; }
    .gc-subtitle { margin: 0; font-size: 14px; color: #6b7280; }

    /* ---- Buttons (wrap, never overlap) ---- */
    .gc-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; }
    .gc-btn { display: inline-block; padding: 10px 16px; border: 1px solid transparent; border-radius: 8px; font-size: 14px; font-weight: 600; line-height: 1.2; text-decoration: none; white-space: nowrap; cursor: pointer; box-sizing: border-box; }
    .gc-btn:focus-visible { outline: 2px solid var(--gc-primary); outline-offset: 2px; }
    .gc-btn-primary { background: var(--gc-primary); color: #fff; }
    .gc-btn-primary:hover { background: var(--gc-primary-hover); color: #fff; }
    .gc-btn-secondary { background: #fff; color: var(--gc-primary); border-color: #bfdbfe; }
    .gc-btn-secondary:hover { background: #eff6ff; }
    .gc-btn-neutral { background: #f3f4f6; color: #374151; border-color: #e5e7eb; }
    .gc-btn-neutral:hover { background: #e5e7eb; }
    /* Reject / stop actions (Decline, Cancel, Close-as-reject) are always red */
    .gc-btn-danger { background: var(--gc-danger); color: #fff; }
    .gc-btn-danger:hover { background: var(--gc-danger-hover); color: #fff; }
    .gc-btn-danger-outline { background: #fff; color: var(--gc-danger); border-color: #fecaca; }
    .gc-btn-danger-outline:hover { background: #fef2f2; }
    .gc-btn-success { background: #16a34a; color: #fff; }
    .gc-btn-success:hover { background: #15803d; }
    .gc-btn-sm { padding: 6px 12px; font-size: 12px; font-weight: 500; border-radius: 6px; }

    /* ---- Stat cards ---- */
    .gc-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    .gc-stat { display: block; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 2px rgba(0,0,0,.04); text-decoration: none; }
    /* Clickable stat card: the whole card is the link */
    a.gc-stat { cursor: pointer; transition: border-color .15s, box-shadow .15s, transform .15s; }
    a.gc-stat:hover { border-color: #bfdbfe; box-shadow: 0 4px 12px rgba(0,56,168,.10); transform: translateY(-1px); }
    a.gc-stat:focus-visible { outline: 2px solid var(--gc-primary); outline-offset: 2px; }
    .gc-stat-hint { margin: 8px 0 0; font-size: 12px; font-weight: 600; color: var(--gc-primary); }
    .gc-stat-label { margin: 0 0 6px; font-size: 13px; font-weight: 500; color: #6b7280; }
    .gc-stat-value { margin: 0; font-size: 28px; font-weight: 700; color: #111827; line-height: 1.1; }
    .gc-stat-value.is-pending { color: #b45309; }
    .gc-stat-value.is-active  { color: var(--gc-primary); }
    .gc-stat-value.is-done    { color: #15803d; }

    /* ---- Tables ---- */
    .gc-table-wrap { overflow-x: auto; }
    .gc-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .gc-table th { padding: 12px 24px; background: #f9fafb; text-align: left; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .gc-table td { padding: 16px 24px; color: #374151; vertical-align: middle; border-top: 1px solid #f3f4f6; }
    .gc-table .gc-col-action { text-align: right; }
    .gc-row-actions { display: flex; justify-content: flex-end; align-items: center; flex-wrap: wrap; gap: 8px; }
    .gc-nowrap { white-space: nowrap; }
    .gc-strong { color: #111827; font-weight: 500; }
    .gc-muted { font-size: 12px; color: #6b7280; }
    .gc-cell-clip { max-width: 240px; }
    .gc-empty { padding: 32px 24px; text-align: center; font-size: 14px; color: #9ca3af; }

    /* ---- Status pills (same mapping in every portal) ---- */
    /* One pill for every status: x-gc-status-badge picks the colour. */
    .gc-badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 500; line-height: 1.4; white-space: nowrap; border: 1px solid transparent; }
    .gc-badge-pending  { background: #fef3c7; color: #b45309; }   /* Pending, Requested */
    .gc-badge-active   { background: #dbeafe; color: #1d4ed8; }   /* Ongoing, Scheduled, Accepted, In Progress */
    .gc-badge-followup { background: #fef9c3; color: #a16207; }   /* For Follow-up */
    .gc-badge-referred { background: #ede9fe; color: #6d28d9; }   /* Referred */
    .gc-badge-done     { background: #dcfce7; color: #15803d; }   /* Completed, Closed, Resolved */
    .gc-badge-danger   { background: #fee2e2; color: #b91c1c; }   /* Declined, Cancelled */
    .gc-badge-neutral  { background: #f3f4f6; color: #4b5563; }
    /* Office tag (Clinic / Disciplinary): outlined so it never looks like a status */
    .gc-office-clinic       { background: #f0fdfa; color: #0f766e; border-color: #99f6e4; }
    .gc-office-disciplinary { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
    .gc-office-neutral      { background: #fff; color: #4b5563; border-color: #e5e7eb; }
    .gc-chip-privacy   { display: inline-block; padding: 8px 12px; border-radius: 8px; background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 500; white-space: nowrap; }

    /* ---- Feedback + footer ---- */
    .gc-flash { padding: 12px 16px; border-radius: 8px; font-size: 14px; }
    .gc-flash-success { background: #dcfce7; color: #15803d; }
    .gc-flash-error   { background: #fee2e2; color: #b91c1c; }
    .gc-footer { margin: 0; padding: 8px 0 0; text-align: center; font-size: 12px; color: #9ca3af; }
    .gc-site-footer { padding: 8px 16px 32px; text-align: center; font-size: 12px; color: #9ca3af; }

    /* ---- Dropdowns: every single-choice <select> shows the same chevron ---- */
    /* !important because many pages set an inline background, which would hide the arrow */
    select:not([multiple]):not([size]), select[size="1"] {
        -webkit-appearance: none !important; appearance: none !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important; background-position: right 10px center !important; background-size: 18px 18px !important;
        padding-right: 36px !important; cursor: pointer;
    }

    /* ---- Content width fallbacks ----
       Some pages use Tailwind width classes that were never compiled into public/build.
       These keep every page on the same shell without needing `npm run build`. */
    .max-w-3xl { max-width: 48rem; } .max-w-4xl { max-width: 56rem; } .max-w-7xl { max-width: 80rem; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .max-w-3xl.mx-auto, .max-w-4xl.mx-auto, .max-w-7xl.mx-auto { padding-left: 16px; padding-right: 16px; box-sizing: border-box; }
    @media (min-width: 640px)  { .max-w-3xl.mx-auto, .max-w-4xl.mx-auto, .max-w-7xl.mx-auto { padding-left: 24px; padding-right: 24px; } }
    @media (min-width: 1024px) { .max-w-3xl.mx-auto, .max-w-4xl.mx-auto, .max-w-7xl.mx-auto { padding-left: 32px; padding-right: 32px; } }
    .py-12 { padding-top: 32px; padding-bottom: 48px; }

    /* ---- Field helper text (shown before any error) ---- */
    .gc-help { margin: 6px 0 0; font-size: 12px; color: #6b7280; }

    /* ---- Light mode only (the system never switches to dark) ---- */
    :root { color-scheme: light; }

    /* ---- App shell: role sidebar on desktop, blue top bar + menu on phones ---- */
    .gc-shell { min-height: 100vh; }
    .gc-shell-main { min-width: 0; display: flex; flex-direction: column; min-height: 100vh; }
    .gc-shell-main > main { flex: 1; }

    .gc-sidebar { display: none; }
    .gc-brand { display: flex; align-items: center; gap: 10px; color: #fff; text-decoration: none; min-width: 0; }
    .gc-brand-mark { width: 36px; height: 36px; flex: none; border-radius: 8px; background: var(--gc-accent); color: var(--gc-primary); display: grid; place-items: center; font-size: 13px; font-weight: 700; }
    .gc-brand-text { display: flex; flex-direction: column; line-height: 1.2; min-width: 0; }
    .gc-brand-text strong { font-size: 15px; font-weight: 600; }
    .gc-brand-text small { font-size: 12px; color: #c9d6f2; }

    .gc-nav { display: flex; flex-direction: column; gap: 2px; }
    .gc-nav-link { display: flex; align-items: center; gap: 12px; width: 100%; min-height: 44px; padding: 10px 14px; box-sizing: border-box;
                   color: #e3eafb; font-size: 14px; font-weight: 500; text-decoration: none; background: transparent; border: 0;
                   border-left: 3px solid transparent; border-radius: 0; cursor: pointer; text-align: left; font-family: inherit; }
    .gc-nav-link:hover { background: rgba(255,255,255,.08); color: #fff; }
    .gc-nav-link:focus-visible { outline: 2px solid var(--gc-accent); outline-offset: -2px; }
    .gc-nav-link.is-active { background: rgba(255,255,255,.16); border-left-color: var(--gc-accent); color: #fff; font-weight: 600; }
    .gc-nav-icon { width: 20px; height: 20px; flex: none; }
    .gc-nav-label { flex: 1; min-width: 0; }
    .gc-nav-count { margin-left: auto; min-width: 22px; padding: 1px 8px; border-radius: 9999px; background: var(--gc-accent); color: var(--gc-primary); font-size: 12px; font-weight: 700; text-align: center; }
    .gc-nav-link-sm { min-height: 40px; padding: 8px 14px; font-size: 13px; }
    .gc-nav-button { font-size: 13px; }

    .gc-side-user { margin-top: auto; padding-top: 12px; border-top: 1px solid rgba(255,255,255,.2); display: flex; flex-direction: column; gap: 2px; }
    .gc-side-who { display: flex; align-items: center; gap: 10px; padding: 4px 14px 8px; color: #fff; }
    .gc-avatar { width: 34px; height: 34px; flex: none; border-radius: 50%; background: #fff; color: var(--gc-primary); display: grid; place-items: center; font-size: 12px; font-weight: 700; }
    .gc-side-name { display: flex; flex-direction: column; min-width: 0; line-height: 1.25; }
    .gc-side-name strong { font-size: 14px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .gc-side-name small { font-size: 12px; color: #c9d6f2; }

    /* Phone / small tablet */
    .gc-topbar { position: sticky; top: 0; z-index: 40; display: flex; align-items: center; justify-content: space-between; gap: 12px;
                 padding: 10px 16px; background: var(--gc-primary); }
    .gc-menu-btn { width: 44px; height: 44px; display: grid; place-items: center; border: 1px solid rgba(255,255,255,.35); border-radius: 8px; background: transparent; color: #fff; cursor: pointer; }
    .gc-menu-btn svg { width: 22px; height: 22px; }
    .gc-menu-btn:focus-visible { outline: 2px solid var(--gc-accent); outline-offset: 2px; }
    .gc-mobile-menu { display: none; position: sticky; top: 64px; z-index: 39; background: var(--gc-primary); padding: 4px 8px 12px; box-shadow: 0 8px 16px rgba(0,0,0,.12); }
    .gc-mobile-menu.is-open { display: flex; flex-direction: column; }
    .gc-mobile-menu .gc-side-user { margin-top: 8px; }

    @media (min-width: 900px) {
        .gc-shell { display: grid; grid-template-columns: 248px minmax(0, 1fr); }
        .gc-sidebar { display: flex; flex-direction: column; gap: 20px; position: sticky; top: 0; height: 100vh; overflow-y: auto;
                      padding: 20px 10px 16px; box-sizing: border-box; background: var(--gc-primary); }
        .gc-sidebar .gc-brand { padding: 0 8px; }
        .gc-topbar, .gc-mobile-menu, .gc-mobile-menu.is-open { display: none; }
    }

    /* ---- Mobile table cards (under 640px): each table row becomes a card ----
       The layout script adds .gc-cards and data-label to every list table.
       !important is needed because many cells carry inline padding/alignment. */
    @media (max-width: 639px) {
        .gc-page { padding: 20px 0 32px; }
        .gc-title { font-size: 21px; }
        .gc-card-header { padding: 14px 16px; }
        .gc-table-wrap, .overflow-x-auto { overflow-x: visible; }
        /* Inline 3- and 4-column stat grids become 2 columns; 2-column forms stack */
        [style*="repeat(4, 1fr)"], [style*="repeat(3, 1fr)"], [style*="repeat(3,1fr)"] { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
        [style*="grid-template-columns: 1fr 1fr"], [style*="grid-template-columns:1fr 1fr"], [style*="repeat(2, 1fr)"] { grid-template-columns: minmax(0, 1fr) !important; }

        table.gc-cards, table.gc-cards tbody { display: block !important; width: 100% !important; }
        table.gc-cards thead { display: none !important; }
        table.gc-cards tr { display: block !important; margin: 12px; padding: 12px 16px; background: #fff !important;
                            border: 1px solid #e5e7eb !important; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
        table.gc-cards td { display: block !important; position: relative; padding: 6px 0 6px 42% !important; border: 0 !important;
                            text-align: right !important; white-space: normal !important; font-size: 14px; max-width: none !important; min-height: 20px; }
        table.gc-cards td::before { content: attr(data-label); position: absolute; left: 0; top: 7px; width: 40%; text-align: left;
                                    font-size: 12px; font-weight: 500; color: #6b7280; text-transform: none; letter-spacing: 0; }
        table.gc-cards td > div, table.gc-cards td > p, table.gc-cards td > span { text-align: right !important; }
        table.gc-cards td.gc-cell-nolabel, table.gc-cards td.gc-cell-full { padding-left: 0 !important; }
        table.gc-cards td.gc-cell-nolabel::before, table.gc-cards td.gc-cell-full::before { content: none; }
        table.gc-cards td.gc-cell-full { text-align: center !important; }
        table.gc-cards td.gc-cell-title { padding: 2px 0 10px !important; margin-bottom: 4px; border-bottom: 1px solid #f3f4f6 !important;
                                          text-align: left !important; font-size: 15px !important; font-weight: 600 !important; color: #111827 !important; }
        table.gc-cards td.gc-cell-title > * { text-align: left !important; }
        table.gc-cards td.gc-cell-title::before { content: none; }
        table.gc-cards td.gc-cell-actions { display: flex !important; flex-wrap: wrap; gap: 8px; padding: 10px 0 2px !important; margin-top: 6px;
                                            border-top: 1px solid #f3f4f6 !important; text-align: left !important; }
        table.gc-cards td.gc-cell-actions::before { content: none; }
        table.gc-cards td.gc-cell-actions > * { flex: 1 1 0; min-width: 0; }
        table.gc-cards td.gc-cell-actions > div, table.gc-cards td.gc-cell-actions .gc-row-actions { display: flex !important; flex-wrap: wrap; gap: 8px; justify-content: stretch !important; }
        table.gc-cards td.gc-cell-actions a, table.gc-cards td.gc-cell-actions button { flex: 1 1 0; min-width: 0; min-height: 40px; padding: 8px 12px !important;
                                          font-size: 13px !important; text-align: center; display: inline-flex !important; align-items: center; justify-content: center; margin: 0 !important; box-sizing: border-box; }
        table.gc-cards td.gc-cell-actions form { display: flex; flex: 1 1 0; min-width: 0; margin: 0 !important; }
        table.gc-cards td.gc-cell-actions form button { width: 100%; }
        table.gc-cards td { overflow-wrap: anywhere; }
        .gc-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .gc-stat { padding: 16px; }
    }
</style>

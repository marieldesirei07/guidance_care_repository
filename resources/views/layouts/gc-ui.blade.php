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
    .gc-link-all { font-size: 13px; font-weight: 600; color: #2563eb; text-decoration: none; white-space: nowrap; }
    .gc-link-all:hover { color: #1e40af; text-decoration: underline; }

    /* ---- Page header (hero) ---- */
    .gc-hero { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    .gc-hero-text { min-width: 0; flex: 1 1 280px; }
    .gc-eyebrow { margin: 0 0 6px; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: .05em; }
    .gc-title { margin: 0 0 4px; font-size: 24px; font-weight: 700; color: #111827; line-height: 1.25; }
    .gc-subtitle { margin: 0; font-size: 14px; color: #6b7280; }

    /* ---- Buttons (wrap, never overlap) ---- */
    .gc-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; }
    .gc-btn { display: inline-block; padding: 10px 16px; border: 1px solid transparent; border-radius: 8px; font-size: 14px; font-weight: 600; line-height: 1.2; text-decoration: none; white-space: nowrap; cursor: pointer; box-sizing: border-box; }
    .gc-btn:focus-visible { outline: 2px solid #2563eb; outline-offset: 2px; }
    .gc-btn-primary { background: #2563eb; color: #fff; }
    .gc-btn-primary:hover { background: #1d4ed8; }
    .gc-btn-secondary { background: #fff; color: #1d4ed8; border-color: #bfdbfe; }
    .gc-btn-secondary:hover { background: #eff6ff; }
    .gc-btn-sm { padding: 6px 12px; font-size: 12px; font-weight: 500; border-radius: 6px; }

    /* ---- Stat cards ---- */
    .gc-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    .gc-stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
    .gc-stat-label { margin: 0 0 6px; font-size: 13px; font-weight: 500; color: #6b7280; }
    .gc-stat-value { margin: 0; font-size: 28px; font-weight: 700; color: #111827; line-height: 1.1; }
    .gc-stat-value.is-pending { color: #b45309; }
    .gc-stat-value.is-active  { color: #1d4ed8; }
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
    .gc-badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 500; white-space: nowrap; }
    .gc-badge-pending  { background: #fef3c7; color: #b45309; }
    .gc-badge-active   { background: #dbeafe; color: #1d4ed8; }
    .gc-badge-followup { background: #ffedd5; color: #c2410c; }
    .gc-badge-done     { background: #dcfce7; color: #15803d; }
    .gc-badge-danger   { background: #fee2e2; color: #b91c1c; }
    .gc-badge-neutral  { background: #e5e7eb; color: #374151; }
    .gc-chip-privacy   { display: inline-block; padding: 8px 12px; border-radius: 8px; background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 500; white-space: nowrap; }

    /* ---- Feedback + footer ---- */
    .gc-flash { padding: 12px 16px; border-radius: 8px; font-size: 14px; }
    .gc-flash-success { background: #dcfce7; color: #15803d; }
    .gc-flash-error   { background: #fee2e2; color: #b91c1c; }
    .gc-footer { margin: 0; padding: 8px 0 0; text-align: center; font-size: 12px; color: #9ca3af; }
</style>

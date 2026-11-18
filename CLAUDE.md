# GuidanceCare — project rules (read first)

Student Guidance, Counseling, Case Monitoring and Referral System for STI College Global City (capstone). Full history, gaps and risks: `docs/PROJECT_HISTORY.md`.

## Stack
Laravel 13.31 (composer.lock), PHP 8.3, Breeze (Blade), Tailwind 3 via PostCSS + Vite, SQLite in dev (`database/database.sqlite`, git-ignored), MySQL planned for production, Laragon on Windows, Mailtrap SMTP. Scheduler: `appointments:send-reminders` daily 08:00 (`routes/console.php`).

## Roles
student, teacher, counselor, clinic, disciplinary, admin (`users.role`). Role alias `role` exists (`bootstrap/app.php` -> `RoleMiddleware`) but is NOT yet applied to routes; all routes sit under `auth` only. Do not assume access control works until that is fixed.

## Privacy walls (never break)
- Clinic and Disciplinary see only office-referral fields: no `case_notes`, no full case narrative, in any query, view or route.
- Emails never contain confidential case notes.
- Every show/update/delete must be scoped by owner or role, not just by id.

## UI rules
- Shared styles live in `resources/views/layouts/gc-ui.blade.php` (included by `layouts/app.blade.php`). Use `gc-*` classes and `<x-gc-section-header>` / `<x-gc-status-badge>`. Do not add one-off inline CSS.
- Page = `gc-page` > `gc-container` > `gc-stack`. Side padding 16/24/32px, 24px between sections, button groups `gc-actions` (wrap, 12px gap).
- Every dashboard table: section header with "View All →", cells 24px/16px, one status-color map (Pending yellow, Accepted/Ongoing blue, Completed/Resolved green, Declined red, For Follow-up orange).
- New Tailwind utility classes do nothing until `npm run build` (compiled CSS in `public/build` is stale).
- Principles: ISO 9241-110 (suitability, self-descriptiveness, error tolerance, consistency), ISO/IEC 25010.

## Data rules
- Students may be unregistered: `cases.student_id` nullable, manual name fields; display with `COALESCE(cases.student_name, users.name)`.
- Statuses — cases: Pending, Ongoing, For Follow-up, Completed, Referred, Closed. office_referrals: Pending, Received, In Progress, Completed, Closed. appointments: Scheduled, Completed, Cancelled, Rescheduled, Requested. counseling_requests (DB CHECK): Pending, Accepted, Scheduled, Closed. Teacher-referral consent is `referrals.student_response`: Pending, Accepted, Declined.
- Controllers mostly use `DB::table()`; only `User` is an Eloquent model. Keep queries in controllers, not Blade.

## Working agreements
- Repo is `C:\laragon\www\guidancecare` (ignore the older `guidance_care` copy).
- Preserve CRLF line endings in files that already use them.
- After changes run: `php artisan view:clear`, `php artisan route:clear`, hard refresh; `npm run build` if Tailwind classes were added.
- Do not touch working behavior: role-based login redirect, unregistered-student COALESCE display, appointment-scheduled email, office-referral limited fields, student accept/decline of teacher referrals.

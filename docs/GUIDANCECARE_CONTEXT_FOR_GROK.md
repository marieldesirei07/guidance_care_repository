# GuidanceCare — System Context Pack (for external review)

*Prepared 20 November 2026 from a full scan of the codebase on the lead developer's laptop (`C:\laragon\www\guidancecare`), live screenshots of every portal (demo seed data), and the Supabase project. Feature lock: Sunday 22 November 2026.*

**How to use this pack:** sections 1–7 describe the system as it is. Section 8 is the audit (what is wrong or risky, ranked). Section 9 is Supabase readiness. Section 10 lists the decisions we want a second opinion on. Screenshots are in `screens/` (numbered; index in section 7).

---

## 1. Project snapshot

| Item | Value |
|---|---|
| Name | GuidanceCare — Student Guidance, Counseling, Case Monitoring and Referral Management System |
| Client | STI College Global City — Guidance Office (capstone project) |
| Framework | Laravel 13.x (`laravel/framework ^13.17`), PHP 8.3, Breeze auth (Blade stack) |
| Front end | Blade templates, Tailwind CSS 3 (+ `@tailwindcss/forms`), Alpine.js, Vite; a shared CSS block `layouts/gc-ui.blade.php` |
| Database (now) | SQLite file `database/database.sqlite` (WAL, busy_timeout 5000) |
| Database (target) | Supabase Postgres 17, project `guidance_care_repository_database_management` (ref `vkthqxegnmiofbzxlwto`, ap-northeast-1). Schema `guidancecare` created (empty, anon/authenticated revoked). |
| Server | Laragon on Windows (Apache). Served at `http://127.0.0.1/guidancecare/public` |
| Mail | Mailtrap sandbox SMTP (free: ~1 email / 10 s, 50 / month) |
| Repo | GitHub `marieldesirei07/guidance_care_repository`, branch `main` (last push `b1ba1e8`, Day 2) |
| Size | 34 migrations, 9 app controllers (+ Breeze auth), 74 web routes (26 are inline closures), ~75 Blade views, 5 Mailables, 1 console command |
| Tests | Only Breeze defaults (ExampleTest, ProfileTest) — no role/feature tests |

## 2. Concept

GuidanceCare replaces paper-based guidance workflows. A student's case moves from **intake** (teacher referral or student request) → **case** (counselor) → **intervention** (notes, appointments) → **handoff** (Clinic or Disciplinary office, need-to-know only) → **closure**.

Fixed product rules:
- Accounts are **school-provisioned** by the admin (STI email format, temporary password, forced change on first login). Public `/register` is disabled (404).
- Students are always real accounts (teacher and counselor pick from a list of student accounts).
- Clinic and Disciplinary see only the office referral (reason + note for office), never counseling notes.
- The system does not replace clinical judgment and does not automate crisis response.

### Roles

| Role | Can do | Cannot see |
|---|---|---|
| Student | Request counseling, request an appointment, view own appointments, accept/decline teacher referrals | Other students, case notes |
| Teacher | Refer a student (picked from accounts), track own referrals | Cases, notes, other teachers' referrals |
| Counselor | Pending queue (referrals, requests, appointment requests), create cases, notes, appointments (+email), office referrals, case status | — (full guidance access) |
| Clinic | Own office referrals; mark completed | Case notes, summaries |
| Disciplinary | Own office referrals; mark resolved | Case notes, summaries |
| Admin | Create/edit/delete accounts, role counts | Case contents |

Demo accounts (seeded, password `Demo@1234`, demo only): students `ramos.356101`, `delacruz.111222`, `santos.333444`, `reyes.555666`, `mendoza.777888` (forced password change); staff `carlo.villanueva` (teacher), `grace.lim` (counselor), `rosa.bautista` (clinic), `mark.aquino` (disciplinary), `guidancecare.admin` — all `@globalcity.sti.edu.ph`.

## 3. Architecture

### 3.1 Current (as built)

```text
Browser (same Wi-Fi/hotspot only)
   │ http://<host-ip>/guidancecare/public
   ▼
Laragon · Apache · PHP 8.3 (Windows laptop)
   │
   ▼
Laravel 13
 ├─ routes/web.php ── auth group ── role:<name> groups (6)
 │     many handlers are route closures with inline DB queries
 ├─ Middleware: RoleMiddleware (403), ForcePasswordChange, TrustProxies('*')
 ├─ Controllers: Counselor\{Case, Appointment, AppointmentRequest, OfficeReferral}
 │               Student\{CounselingRequest, AppointmentRequest, TeacherReferral}
 │               Teacher\Referral
 ├─ Data access: DB::table() query builder everywhere (only User is an Eloquent model)
 ├─ Views: Blade; dashboards also run DB:: queries inside the view
 ├─ Mail: synchronous Mail::send with 1.1 s gaps (Mailtrap limit)
 └─ Scheduler: appointments:send-reminders daily 08:00 (no cron configured)
   │
   ▼
SQLite file (WAL)
```

### 3.2 Target

```text
Any device, any network ── HTTPS
   ▼
Cloudflare Quick Tunnel (demo)  or  real host later (Laravel Cloud/Forge/VPS)
   ▼
Laragon → Laravel (same app; auth stays in Laravel, roles enforced in Laravel)
   ▼  pgsql, SSL, Supabase Session pooler :5432 (not 6543 — Laravel needs prepared statements)
Supabase Postgres 17 · schema guidancecare · RLS on, no policies · anon/authenticated revoked
```

Network rule: same Wi-Fi is required only while the app is reached by the laptop's LAN IP. Supabase alone does not remove it; a tunnel or hosting does.

### 3.3 Connectors and external pieces

| Piece | Status | Notes |
|---|---|---|
| Laragon (Apache + PHP 8.3) | Working | `.env` re-read races on Windows → always `php artisan config:cache`; view compile race → `php artisan view:cache` (see A5) |
| SQLite | Working | Demo data via `DemoUsersSeeder` + `DemoDataSeeder` |
| Mailtrap SMTP | Working, rate-limited | Student copy of referral email off (`GC_REFERRAL_EMAIL_STUDENT=false`) |
| GitHub | Working | `.env`, `*.sqlite`, `vendor`, `node_modules`, `public/build` ignored |
| Supabase (via MCP connector) | Connected | Schema created; no tables yet |
| Cloudflare Quick Tunnel | Documented, not yet run | `docs/PUBLIC_ACCESS.md`; `trustProxies(at: '*')` added (uncommitted) |
| Laravel scheduler | Not running | Needs Windows Task Scheduler/cron for reminders |
| Laravel Boost MCP | Failing | `php` not on Windows PATH |

## 4. Data model (final state after all migrations)

```text
users ──┬─< referrals (teacher_id, student_id)
        ├─< counseling_requests (student_id)
        ├─< appointment_requests (student_id, counselor_id)
        └─< cases (student_id, counselor_id)
                 ├─< appointments (case_id)          ← student_id has NO FK
                 ├─< case_notes (case_id)
                 ├─< office_referrals (case_id, referred_by)
                 └─< appointment_requests (case_id)
cases.referral_id / cases.request_id → plain integers, NO FK
```

| Table | Key columns | Status / constraints |
|---|---|---|
| users | name, email (unique), role, student_number, employee_id, must_change_password, course_id, year_level, section_id | role enum: student, teacher, counselor, clinic, disciplinary, admin |
| referrals | teacher_id→users (cascade), student_id→users (cascade, nullable), student_name, student_number, year_section, concern_type, description, urgency, student_response | status: Pending, Accepted, In Progress, Closed · urgency: Low/Medium/High · student_response: free string (Pending/Accepted/Declined) |
| counseling_requests | student_id→users (cascade), student_name, student_number, program, year_section, concern_type, description, preferred_datetime, is_anonymous | status: Pending, Accepted, Scheduled, Closed |
| cases | case_number (unique), student_id→users (**cascade**), student_name/number/year_section copies, counselor_id (set null), referral_id, request_id, concern_type, summary, opened_at/closed_at (date) | status: Pending, Ongoing, For Follow-up, Completed, Referred, Closed |
| appointments | case_id→cases (cascade), student_id (no FK), counselor_id (set null), appointment_datetime, location, purpose, reminder_sent_at | status: free string (Scheduled, Completed, Cancelled, Rescheduled) |
| case_notes | case_id→cases (cascade), counselor_id (set null), note | — |
| office_referrals | case_id→cases (cascade), referred_by→users (cascade), office (free string), reason, notes_for_office | status: Pending, Received, In Progress, Completed, Closed |
| appointment_requests | student_id (cascade), case_id (null on delete), preferred_datetime, purpose, counselor_id, appointment_id, counselor_remarks | status: free string(20) — Pending, Scheduled, Declined |
| Unused | student_profiles, courses, sections | Created but not used by any screen |
| Laravel | sessions, cache, jobs, password_reset_tokens | sessions/cache use the database driver |

## 5. Route map (by role)

- **Shared:** `/login`, `/logout`, `/dashboard` (redirect by role), `/password/change`, `/` (still the default Laravel welcome page).
- **Student:** dashboard; `request` (create/store); `requests` (index/show/**delete**); `appointments` (index/show/**delete**); `appointments/request` (create/store); `teacher-referrals` (index/show/respond/**delete**).
- **Teacher:** dashboard; `referrals` (index/create/store/show/**delete**).
- **Counselor:** dashboard; `pending` (+ referral/request/appointment-request detail, accept, decline, delete); `cases` (create/store/index/show/status/**delete**, from-referral, from-request, notes); `appointments` (index/create/store/status); `office-referrals` (index/show/**delete**, create/store); `referrals`, `requests` (index/**delete**).
- **Clinic / Disciplinary:** dashboard; `referrals` (index/show); `referrals/{id}/complete` (POST, scoped to own office).
- **Admin:** dashboard; `users` (index/create/store/edit/update/**delete**).

## 6. Main workflows

1. **Teacher referral:** Teacher picks student → referral (Pending) → email to all counselors → student may Accept/Decline → counselor opens it → Create Case (case Ongoing, referral Accepted) or Decline (Closed).
2. **Student request:** Student submits concern (+ optional preferred time) → counselor Create Case → redirected to appointment form prefilled with the preferred time → appointment Scheduled + email to student.
3. **Appointment request:** Student requests a time (one pending at a time) → counselor accepts (link to an open case or open a new one) and schedules, or declines with remarks the student sees.
4. **Office handoff:** Counselor creates office referral (Clinic/Disciplinary/Other) → case status Referred → office sees reason + note for office → marks Completed/Resolved.
5. **Account lifecycle:** Admin creates account with temp password → `must_change_password` → user forced to `/password/change` on first login.

## 7. UI / UX

### 7.1 Design system as built

- `layouts/app.blade.php` (Breeze shell) → includes `layouts/gc-ui.blade.php`, a **plain `<style>` block** of `gc-*` classes (page, container 80rem, stack 24px, card, hero, buttons, stat cards, tables, badges). Chosen so new classes work without `npm run build`.
- `components/gc-status-badge` maps every status to one colour (pending amber, active blue, follow-up, done green, danger red, neutral). `components/gc-section-header` exists.
- Only the student, clinic and disciplinary dashboards are written with `gc-*` classes. Counselor, teacher, admin pages and most forms use **inline styles with hard-coded hex** (17+ distinct colours on some pages). The result looks similar but is implemented two ways.
- Primary colour in use is Tailwind blue `#2563eb`, not the STI blue `#0038A8` in the blueprint. Accent yellow appears ad hoc (`#f7b900` buttons on case detail). Tailwind config has no brand tokens.
- Top navigation shows only the Laravel logo, "Dashboard" and the user menu (Change Password, Log Out). There is no per-role menu; pages are reached through dashboard cards and "View All →" links.
- Login and guest pages still use the Laravel logo; the browser title is "Laravel" (`APP_NAME`).

### 7.2 Page archetypes (wireframes)

```text
A. DASHBOARD (full width, 80rem)          B. LIST (full width)
┌──────────────────────────────────────┐  ┌──────────────────────────────────────┐
│ EYEBROW · PORTAL         [Primary +] │  │ EYEBROW · Title        [← Back][+ Add]│
│ Title / subtitle                     │  │ subtitle                              │
├────────┬────────┬────────┬───────────┤  ├──────────────────────────────────────┤
│ Stat   │ Stat   │ Stat   │ Stat      │  │ [All][Today][Upcoming] or [Search][▼] │
├────────┴────────┴────────┴───────────┤  ├──────────────────────────────────────┤
│ Card title                 View All →│  │ COL  COL  COL  STATUS(pill)   ACTION  │
│ COL  COL  COL  STATUS(pill)   [View] │  │ row ...                [View][🗑]     │
│ row ...                              │  └──────────────────────────────────────┘
└──────────────────────────────────────┘

C. DETAIL (narrow, ~max-w-2xl)            D. FORM (narrow)
┌──────────────────────────┐              ┌──────────────────────────┐
│ EYEBROW      [← Back]    │              │ EYEBROW / Title           │
│ Title · meta             │              │ [Searchable student list] │
│ field  field  field      │              │ field    field            │
│ ┌ description box ─────┐ │              │ textarea                  │
│ └──────────────────────┘ │              │ [Submit] [Cancel]         │
│        [Primary][Danger] │              └──────────────────────────┘
└──────────────────────────┘
```

### 7.3 Screenshot index (`screens/`)

| # | Page | What to notice |
|---|---|---|
| 01 | Login | Default Breeze page with Laravel logo; no GuidanceCare branding |
| 02 | Counselor dashboard | 4 stat cards, cases table, active office referrals; "Office" column says Guidance even for Referred cases |
| 03 | Counselor pending queue | Three cards: teacher referrals, counseling requests, appointment requests; trash icon beside every row |
| 04 | Counselor referral detail | Narrow card; Create Case / Decline; student response shown |
| 05 | Counselor create case | Searchable student picker (account list), concern, summary |
| 06 | Counselor cases list | Search + status filter (filtering done in PHP memory, no pagination) |
| 07 | Counselor case detail | Status update, appointments, notes; yellow buttons; "Office Referral" button sits in the Case Notes header |
| 08 | Counselor appointments | All/Today/Upcoming/Past tabs, inline status update |
| 09 | Counselor office referrals | Office pill, status pill, delete |
| 10 | Teacher dashboard | Totals, my referrals |
| 11 | Teacher refer student | Picker shows every student's name, number and email |
| 12 | Student dashboard | Request Counseling / Request Appointment / Teacher Referrals |
| 13 | Student request counseling | Name and student number are **editable** text fields |
| 14 | Student appointments | Scheduled appointment has a delete icon; declined request shows counselor remarks |
| 15 | Student teacher referrals | Accept/decline; delete icon |
| 16 | Clinic dashboard | "Privacy Protected" chip; shows case concern type (e.g. Emotional) |
| 17 | Clinic referral detail | Reason + note for office; "Linked Guidance Case" is blue but not a link |
| 18 | Disciplinary dashboard | Same pattern as Clinic ("Mark Resolved") |
| 19 | Admin dashboard | Counts per role + cases; quick actions |
| 20 | Admin users | Role filter chips; edit/delete |
| 21 | Admin users (mobile 375 px) | Table scrolls sideways; actions off-screen |

## 8. Audit findings (ranked)

### A. Blockers for the Supabase (Postgres) migration

| ID | Finding | Evidence | Fix |
|---|---|---|---|
| A1 | **Migration chain will fail on Postgres.** Later "fix" migrations `dropIfExists` tables that other tables reference by foreign key. SQLite allowed it; Postgres refuses ("cannot drop table … other objects depend on it"). | `2026_09_30_132300_fix_referrals_table` drops `referrals` (referenced by `cases.referral_id`); `2026_09_30_132830_fix_counseling_requests_table` drops `counseling_requests` (referenced by `cases.request_id`); `2026_10_04_031259_fix_cases_table` drops `cases` (referenced by `appointments`, `case_notes`, `office_referrals`). | Squash the 34 migrations into one clean baseline set that creates the **final** schema directly (section 4), with real FKs and CHECK constraints. Keep the old files only for the SQLite build. |
| A2 | `appointments` rebuild migration uses `CREATE TABLE … AS SELECT` and inserts explicit ids. Harmless on an empty DB, but on Postgres the id sequence is not advanced if rows exist. | `2026_10_13_100525_make_appointments_student_id_nullable` | Removed by the baseline in A1. |
| A3 | Several `->change()` and `->after()` calls. `after()` is ignored on Postgres; `change()` re-creates types. | referrals/cases nullable changes | Removed by the baseline. |
| A4 | Missing FKs and free-text statuses: `appointments.student_id`, `cases.referral_id`, `cases.request_id` have no FK; `appointments.status`, `appointment_requests.status`, `office_referrals.office`, `referrals.student_response` have no CHECK. | final schema | Add in the baseline. |
| A5 | Ready already: no `LIKE`, `PRAGMA`, `strftime` or SQLite-only functions in queries; `COALESCE` and `CAST(… AS TEXT)` work on Postgres; booleans go through casts. | grep of app/routes/seeders | No change needed. |

### B. Security, privacy and data integrity

| ID | Severity | Finding | Recommendation |
|---|---|---|---|
| B1 | High | **Deleting a user destroys counseling records.** `cases.student_id` is `ON DELETE CASCADE`, so admin deleting a student also deletes their cases, notes (via cases), appointments, requests and referrals. | Never hard-delete accounts: add `is_active` / soft deletes; use `restrictOnDelete` on `cases.student_id`. |
| B2 | High | **Students can delete records others depend on**: a counselor-scheduled appointment and a teacher's referral about them (`student.appointments.destroy`, `student.teacher_referrals.destroy`). | Replace with "Cancel request" / "Decline" that changes status; keep the row. |
| B3 | High | Counselor routes hard-delete referrals, requests, cases and office referrals with no trail. | Soft deletes or "Closed/Archived" status; restrict deletes to admin. |
| B4 | High | No audit log of who created, viewed, changed or closed confidential records (needed for Data Privacy Act accountability). | `activity_log` table written on create/update/status change/delete and on case views. |
| B5 | Medium | Clinic/Disciplinary see the case's `concern_type` (e.g. "Emotional") and case number. | Show only reason + note for office, unless the counselor marks it shareable. |
| B6 | Medium | Office "Other" is allowed but has no portal, so those referrals go nowhere. | Remove "Other" or route it to the counselor. |
| B7 | Medium | Student request form lets the student edit name and student number; stored copies (`cases.student_name`) take priority over the account in `COALESCE(cases.student_name, users.name)`. | Read identity from the account only; drop the copy columns or reverse the COALESCE. |
| B8 | Medium | Office "complete" has no status guard and does not record who or when (`handled_by`, `completed_at` exist only in the dropped original table). | Guard the transition; store handler and time. |
| B9 | Low | Teacher's student picker lists every student's email and number. | Acceptable for a school directory; mention in privacy notes. |
| B10 | OK | Role isolation verified live: student → counselor/clinic/admin URLs return 403; `/register` 404; teacher/student detail queries are scoped to the owner; office queries scoped to the office. | — |

### C. Reliability and operations

| ID | Finding | Fix |
|---|---|---|
| C1 | Parallel requests on Windows return **500** (log: `rename(...storage/framework/views/...tmp): Access is denied` — two requests compiling the same Blade view). Seen today when 6 pages loaded at once. | Run `php artisan view:cache` (and `config:cache`, `route:cache`) after every deploy and before demos. |
| C2 | SQLite "database is locked" seen earlier under concurrent writes (mitigated with WAL + busy_timeout). | Goes away on Postgres. |
| C3 | Reminder emails are scheduled (`dailyAt 08:00`) but nothing runs the scheduler on Laragon. | Windows Task Scheduler running `php artisan schedule:run` every minute, or drop the feature claim. |
| C4 | Emails are sent inside the web request with sleeps (Mailtrap limit). | Queue mail (`ShouldQueue` + `queue:work`) after lock, or keep and document. |
| C5 | Test suite: only Breeze defaults; `ProfileTest` targets profile routes that no longer exist (fails). | Delete it; add feature tests for role isolation and the five workflows. |

### D. Code structure (maintainability)

| ID | Finding | Fix (post-lock unless cheap) |
|---|---|---|
| D1 | Business logic in route closures (26 of 74 handlers in `routes/web.php`, incl. all admin user CRUD) and **DB queries inside Blade views** (counselor dashboard 11, admin 7, teacher 6, student 5, clinic 3, disciplinary 3). | Move to controllers; pass data to views. |
| D2 | `CaseController` is 26 KB and also serves Clinic and Disciplinary pages. | Split per role/portal. |
| D3 | Only `User` is an Eloquent model; no FormRequests, no Policies. | Models for each table; policies for ownership. |
| D4 | No pagination; case search loads every case and filters in PHP. | `paginate()` + DB-side search (`ILIKE` on Postgres). |
| D5 | Unused code: `RegisteredUserController`, `auth/register.blade.php`, `ProfileController`, `student_profiles`/`courses`/`sections` tables, welcome page. | Remove or document. |

### E. UI / UX

| ID | Finding | Fix |
|---|---|---|
| E1 | Two implementations of the same look (gc-ui classes vs inline hex). | Blade components (`x-gc.card`, `x-gc.table`, `x-gc.button`, `x-gc.badge`, `x-gc.stat`) and convert counselor/teacher/admin pages. |
| E2 | Brand colours not applied (`#2563eb` instead of STI `#0038A8`; yellow used inconsistently). | Tokens in one place (gc-ui variables or Tailwind theme). |
| E3 | No role navigation menu. | Role menu from one config array in `navigation.blade.php`. |
| E4 | Laravel branding on login, title "Laravel". | GuidanceCare logo, `APP_NAME=GuidanceCare`, branded login. |
| E5 | Small inconsistencies: Office column says Guidance for referred cases; non-clickable blue "Linked Guidance Case"; Office Referral button inside Case Notes header; icon-only delete buttons; some pages without footer. | Fix during the component pass. |
| E6 | Mobile: tables overflow; actions off-screen at 375 px. | Card layout under 640 px, or sticky action column. |

## 9. Supabase readiness summary

- **App code:** largely portable (query builder, no SQLite-only SQL).
- **Migrations:** not portable as-is (A1). A consolidated baseline migration for Postgres is the main work item.
- **Config:** `pgsql` connection needs `search_path` from `DB_SCHEMA` (currently hard-coded `'public'`) and `sslmode=require`; Laragon needs `pdo_pgsql`.
- **Security:** RLS on with no policies, no grants to `anon`/`authenticated`; the Laravel DB role owns the tables. Only demo data until the school approves cloud storage of counseling records (Data Privacy Act of 2012).
- **Data:** no real data to move; rebuild from seeders.

## 10. Questions we want a second opinion on

1. Squash all migrations into a Postgres baseline now, or keep the chain and patch the three failing drops?
2. With the lock on Sunday, which of B1–B4 (delete cascades, student deletes, hard deletes, audit log) must be fixed before the defense?
3. Should Clinic/Disciplinary see the concern type at all (B5)?
4. Convert counselor/teacher/admin pages to shared Blade components before lock, or only fix brand colour + navigation + login branding?
5. For the demo: Supabase + Cloudflare Quick Tunnel from the laptop, or keep SQLite on LAN as primary and Supabase as a shown "deployment-ready" branch?

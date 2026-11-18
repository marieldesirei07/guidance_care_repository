# GuidanceCare — Pre-Lock Audit

**Written:** Tue 17 Nov 2026 (evening, before Day 1)
**Source of truth:** the live repo at `C:\laragon\www\guidancecare`, checked against the unified handoff.
**Feature lock:** Sun 22 Nov 2026, end of day.

> **Date correction:** the handoff labels Day 1 as "Tue 18 Nov". 18 Nov 2026 is a **Wednesday**.
> The real sprint is Day 1 Wed 18 → Day 2 Thu 19 → Day 3 Fri 20 → Day 4 Sat 21 → Day 5 Sun 22 (lock).

Status key: **DONE** = fixed and verified · **OPEN** = still to do · **DECIDE** = needs a team decision · **CUT** = out of scope

---

## 1. Handoff claims that are now out of date

| Handoff says | Live repo shows |
|---|---|
| Laravel "13" unconfirmed | **Laravel 13.31.0** (from `composer.lock`) |
| Role middleware missing; routes `auth` only | `role` alias registered in `bootstrap/app.php`; all six portal groups use `role:<name>`. Tested 17 Nov: the teacher account gets 403 on student, counselor, clinic, disciplinary and admin pages. |
| Admin is a shell with no user CRUD | Admin **create, edit and delete user** exist (`routes/web.php`), now with the password rule and email allowlist. |
| Clinic/Disciplinary complete via GET | Now **POST + CSRF**; a GET returns 405 (verified by the team). |
| Student Teacher Referrals list is empty | Fixed in `Student/TeacherReferralController::index()`; the view gets `$referrals`. |
| Writing "Declined" breaks CHECK | Fixed: decline now writes `Closed` on both `counseling_requests` and `referrals.status`. |
| Email payloads mismatched; only "scheduled" proven | Fixed `AppointmentReminder`, `ReferralAccepted`, `TeacherReferralNotification` (`public array $data`). Scheduled, reminder and referral-accepted emails were confirmed in Mailtrap. |
| Reminder scheduler unverified | `routes/console.php`: `appointments:send-reminders` daily at 08:00; the command ran manually and the email arrived. |
| `migrate:fresh` fragile | **`migrate:fresh --seed` succeeds on a clean SQLite file** (17 Nov), after making `2026_09_17_133052_add_student_number_to_users_table` skip existing columns. |
| Seeder missing | `DemoUsersSeeder` creates the six role accounts (sample data still OPEN, see C4). |

## 2. Fixed this session (evidence)

| # | Item | Files | Evidence |
|---|---|---|---|
| F1 | Role middleware on all portals | `routes/web.php` | Teacher gets 403 on 10 foreign URLs |
| F2 | Student teacher-referrals data | `Student/TeacherReferralController.php` | Rows shown |
| F3 | Email payload keys | `app/Mail/*.php` | Mailtrap screenshots |
| F4 | Declined → Closed | `Counselor/CaseController.php` | No more 500 |
| F5 | Complete/resolve via POST | `routes/web.php`, clinic + disciplinary views | GET returns 405 |
| F6 | Clinic/Disciplinary privacy | `CaseController` (column whitelist), views, delete routes removed | No `case_notes` join; only reason + `notes_for_office` shown |
| F7 | Privacy hint on office-referral form | `counselor/office_referrals/create.blade.php` | Visual |
| F8 | Password rule (8+, upper, lower, number, symbol) | `AppServiceProvider`, admin and register forms | Code review |
| F9 | School-email allowlist | `config/guidancecare.php`, `app/Rules/AllowedEmailDomain.php`, register, profile, admin | Code review |
| F10 | **Student request form always failed validation** (server wanted program, year_level and section; the form sends year_section) | `Student/CounselingRequestController.php` | Submitted via browser; it appears in the counselor queue |
| F11 | Fresh install broken (duplicate `student_number`) | `2026_09_17_133052_…php` | `migrate:fresh --seed` passes |
| F12 | Demo accounts seeder | `database/seeders/DemoUsersSeeder.php`, `DatabaseSeeder.php` | Seeder DONE |

## 3. Open findings

| ID | Severity | Finding | File(s) | Day |
|---|---|---|---|---|
| C1 | Critical | **Live-demo path not yet fully run**: student request reaches the counselor (verified); teacher referral was submitted, and the counselor check is pending. The case, note, appointment, office referral and completion path is not yet run end to end in the browser. | — | 1 |
| C2 | Critical | Demo must run from a seed, not leftover rows; the seed has users only. | `database/seeders` | 1–2 |
| H1 | High | **Appointment-before-case**: `appointments.case_id` is NOT NULL, but the student "Request Appointment" page exists (`AppointmentRequestController`). Flow unverified. | migrations, `Student/AppointmentRequestController.php` | 2 (DECIDE) |
| H2 | High | Public `/register` is open (accounts default to the `student` role; the allowlist now limits domains). | `routes/auth.php` | 1 (DECIDE) |
| H3 | High | MySQL never tested. Risky migrations: `2026_10_13_100525` (raw `CREATE TABLE … AS SELECT`), the `->change()` calls, the drop-and-recreate "fix" migrations. | `database/migrations` | 4 |
| H4 | High | Counselor delete routes for referrals and requests have no scoping beyond role (any counselor can delete any row). Acceptable for a single guidance office; a soft-delete or confirmation is recommended. | `routes/web.php` L274–282 | 2 |
| M1 | Medium | `TeacherReferralNotification` is never sent (import only). | `Teacher/ReferralController.php` | 2 |
| M2 | Medium | Counselor, teacher and admin pages are not yet on `gc-ui`. | `resources/views/{counselor,teacher,admin}` | 3 |
| M3 | Medium | Queries inside Blade (e.g. student dashboard counts). | views | 3 |
| M4 | Medium | "View Details" controls not audited page by page. | views | 2–3 |
| M5 | Medium | One shared helper for student display name (COALESCE) not yet introduced. | controllers | 2 |
| L1 | Low | Friendly page for 419 (expired login token). | `bootstrap/app.php` | 5 |
| L2 | Low | Stray file named `save()` in the repo root. | root | 1 |
| L3 | Low | `docs/PROJECT_HISTORY.md` still says admin has no CRUD. | docs | 1 |
| X1 | — | `course_id` / SHS–Tertiary filter. | — | **CUT** unless the paper demo needs it |

## 4. Decisions

**Decided 17 Nov 2026:**
1. **H1 → separate `appointment_requests` table.** The student requests; the counselor accepts, links or creates a case, then schedules a real appointment.
2. **H2 → keep public registration**, limited by the school-email allowlist; new accounts are always the `student` role.
3. **X1 → course/SHS filter CUT.**

**Still open:**
4. **H3:** is MySQL on Laragon available for Day 4?
5. Will Chapter 3 be revised to match the post-paper features, or should the demo script avoid them?

## 5. Demo accounts (local only)

`student@`, `teacher@`, `counselor@`, `clinic@`, `disciplinary@`, `admin@` + `test.com`, password `Demo@1234` (meets the password rule). Change it before any public hosting.

# GuidanceCare — Feature Lock Log

Sprint: Wed 18 Nov → Sun 22 Nov 2026 (feature lock end of day Sunday).

---

## Day 1 — Wednesday, 18 November 2026

**Focus:** P1: live demo path, demo data, account rules (product correction).

### Completed
- **Product correction applied:** school-provisioned accounts only.
  - Public `/register` removed (404).
  - Temporary passwords: `users.must_change_password`, forced Change Password page, flag set by Admin create/reset.
  - Teacher referral and counselor manual case must pick an existing student account (searchable picker).
  - Demo accounts in STI format (`{lastname}.{number}@globalcity.sti.edu.ph`).
  - Allowlist default is now only `globalcity.sti.edu.ph`.
- **Demo seeder:** `DemoUsersSeeder` (10 accounts) + `DemoDataSeeder` (pending referrals and request, 3 cases with notes, appointment, Clinic and Disciplinary referrals).
- **Bugs fixed:**
  - SQLite "database is locked" on login → `busy_timeout` 5000 ms + WAL (`config/database.php`).
  - App time was UTC (cases showed 02:22 AM at 10:22 AM) → `Asia/Manila` (`config/app.php`).
  - Student's preferred time not filled into the appointment form (`counselor/appointments/create.blade.php`).
- Docs: `SMOKE_TEST.md` written and updated; `PRE_LOCK_AUDIT.md` section 6; `PROJECT_HISTORY.md` corrected.
- Stray `save()` file deleted.

### Smoke test (browser, lead's laptop, SQLite)
| Area | Result |
|---|---|
| Teacher referral reaches counselor queue | PASS |
| Counselor: case from referral, case from request, status, note, appointment + email sent, Clinic and Disciplinary referral, decline | PASS |
| Counselor: manual case requires a student account; search filter works | PASS |
| Clinic: only own referrals; reason and Guidance note shown; no case notes; other office / counselor pages 403; GET complete 405; mark completed | PASS |
| Disciplinary: same checks; mark resolved | PASS |
| Student: dashboard, accept teacher referral, new request with preferred time, other students' records not reachable, other portals 403 | PASS |
| Temporary password: forced to Change Password; after change, full access | PASS (weak-password rejection checked through Admin, same rule) |
| Admin: weak password rejected, Gmail rejected, valid school account created with temporary password, self-delete refused, other portals 403 | PASS |
| `/register` | 404 (PASS) |
| Mailtrap: appointment email (correct details, no notes), referral-accepted email | PASS (second counselor's copy likely dropped by the Mailtrap free-plan per-second limit; one counselor after re-seed) |
| Preferred time loads into appointment form (after fix) | PASS (Nov 24, 1:30 PM pre-filled) |

### Files touched
`config/database.php`, `config/app.php`, `config/guidancecare.php`, `.env.example`, `bootstrap/app.php`, `routes/web.php`, `routes/auth.php`, `app/Models/User.php`, `app/Http/Middleware/ForcePasswordChange.php` (new), `app/Http/Controllers/Auth/PasswordController.php`, `app/Http/Controllers/Teacher/ReferralController.php`, `app/Http/Controllers/Counselor/CaseController.php`, `database/migrations/2026_11_18_000001_add_must_change_password_to_users_table.php` (new), `database/seeders/{DatabaseSeeder,DemoUsersSeeder,DemoDataSeeder}.php`, `resources/views/auth/change-password.blade.php` (new), `resources/views/partials/student-picker.blade.php` (new), `resources/views/teacher/referrals/create.blade.php`, `resources/views/counselor/cases/create.blade.php`, `resources/views/counselor/appointments/create.blade.php`, `resources/views/layouts/navigation.blade.php`, `docs/*`.

### Database
SQLite (dev). Test rows created today are marked "TEST" and disappear on the next `migrate:fresh --seed`.

### Remaining risks
- `referrals.student_id` / `cases.student_id` still nullable in the schema (older rows); new rows always set it.
- Rows created before the timezone fix show times 8 hours early (fixed by re-seeding).
- Teacher-referral email to the counselor still not sent (Day 2).
- Seeder not yet run with `migrate:fresh --seed` after today's changes (end-of-day check).

### Plan for tomorrow (Day 2, Thu 19 Nov)
Appointment-request table and flow; teacher-referral email to counselor; View Details audit; counselor delete confirmations; members clone the repo; 3-laptop LAN test.

---

## Day 2 — Thursday, 19 November 2026 (evening)

**Focus:** P1: appointment requests, referral notifications, button audit.

### Completed
- **Appointment requests** (decision of 17 Nov): new `appointment_requests` table (Pending → Scheduled / Declined).
  - Student: "Request Appointment" button (dashboard and My Appointments); can link only their own open case; future time required; one pending request at a time; "My Appointment Requests" list with status and counselor remarks.
  - Counselor: "Appointment Requests" section in the Pending Queue; details page; **Accept & Schedule** (pick the student's open case or open a new one → appointment form with the preferred time filled in → saving marks the request Scheduled); **Decline** with optional remarks.
  - Old student request code that saved appointments without a case (and accepted any case number) was replaced.
- **Teacher-referral emails:** counselor gets "New teacher referral" (no description text, link to the Pending Queue). Student copy exists but is **off by default** (`GC_REFERRAL_EMAIL_STUDENT=false`) because Mailtrap's free inbox allows about one email per 10 seconds; turn on for final testing with a real mail server.
- **Button audit:** all 21 "View Details" buttons link to real pages; all 11 delete forms ask for confirmation. No changes needed.
- **Fixed:** intermittent 500 "No application encryption key" (Windows/Apache missing `.env` on overlapping requests) → settings are now cached with `php artisan config:cache` (re-run after any `.env` or config change).
- Docs: `SMOKE_TEST.md` updated (S8–S11, T2c, C10–C11); `TWO_LAPTOP_TEST.md` written for Friday.

### Smoke test (browser, lead's laptop, SQLite, fresh seed)
| Area | Result |
|---|---|
| Counselor: appointment request queue, details, validation (no concern type / other student's case rejected) | PASS |
| Counselor: accept with new case → preferred time pre-filled → scheduled → email to student | PASS |
| Counselor: decline with remarks | PASS |
| Student: request form (own cases only), pending list, duplicate blocked, past date rejected, other student's case rejected | PASS |
| Student: declined request shows remarks | PASS |
| Student: Submit Request button clicked by the lead | PASS (shows validation message, keeps typed values) |
| Teacher: referral without student rejected; referral saved; counselor email sent; no error | PASS (after config:cache and email switch) |
| Mailtrap: appointment email (Juan, Nov 23 3:00 PM), student referral notice (Paolo) | PASS |

### Files touched
`database/migrations/2026_11_19_000001_create_appointment_requests_table.php` (new), `app/Http/Controllers/Student/AppointmentRequestController.php`, `app/Http/Controllers/Counselor/AppointmentRequestController.php` (new), `app/Http/Controllers/Counselor/AppointmentController.php`, `app/Http/Controllers/Counselor/CaseController.php`, `app/Http/Controllers/Teacher/ReferralController.php`, `app/Mail/NewReferralForCounselor.php` (new), `config/guidancecare.php`, `.env.example`, `routes/web.php`, `database/seeders/DemoDataSeeder.php`, views: `counselor/appointment_requests/show` (new), `counselor/cases/pending`, `counselor/appointments/create`, `student/appointments/index`, `student/appointments/request`, `student/dashboard`, `emails/new_referral_counselor` (new), `emails/teacher_referral`, `docs/*`.

### Remaining risks
- Mailtrap free plan: 50 emails a month, about 1 per 10 seconds; plan the demo's emails.
- Settings cache must be refreshed (`php artisan config:cache`) after every `.env` change, on every laptop.
- 2-laptop LAN test not yet done (Friday).

### Plan for tomorrow (Friday, 20 Nov — Day 2 test + Day 3)
2-laptop LAN test with one member (`docs/TWO_LAPTOP_TEST.md`); member clones the repository; UI pass on counselor, teacher and admin pages; admin summary counts.

## Day 3 — Friday, 20 November 2026

### 2-laptop LAN test (SQLite, fresh seed at 3:59 PM)
Host: Mariel's laptop (Laragon, `http://172.20.10.6/guidancecare/public`). Member: second laptop on the same network, browser only. Every step was checked against the database after it was done.

| # | Step | Result | Time | Database check |
|---|---|---|---|---|
| L1 | Student (Ana) submits counseling request | PASS | 4:17 PM | counseling_requests #3, Pending |
| L2 | Counselor sees it in Pending Queue | PASS | 4:17 PM | — |
| L3 | Teacher refers Maria Santos | PASS | 4:23 PM | referrals #4, Behavioral, High, Pending |
| L4 | Counselor sees referral; Mailtrap "New teacher referral" | PASS | 4:23 PM | email received within seconds |
| L5 | Student requests appointment (linked to CASE-DEMO01) | PASS | 4:28 PM | appointment_requests #3, Pending |
| L6 | Counselor accepts and schedules; email to student | PASS | 4:30 PM | appointments #2 Scheduled; request #3 Scheduled, appointment_id 2 |
| L7 | Student sees appointment; request shows Scheduled | PASS | 4:32 PM | — |
| L8 | Case from L3 referral; office referral to Clinic | PASS | 4:55 PM | CASE-EYKRHF Referred; referral #4 Accepted; office_referrals #3 Clinic |
| L9 | Clinic sees it, no case notes, Mark Completed | PASS | 4:57 PM | office_referrals #3 Completed |
| L10 | Simultaneous student submit, teacher submit, counselor page load | PASS | 5:03 PM | counseling_requests #4, referrals #5; no error page (after `view:cache`) |
| L11 | Student opens `/counselor/dashboard` | PASS | 5:07 PM | 403 Unauthorized access |

**Result: 11 of 11 passed.** This is the SQLite baseline the Supabase build will be compared against.

### Known issues seen during the test (already on the audit list, not failures)
- Clinic list and detail still show the case concern type (B5, to hide).
- "CASE-…" on the Clinic detail page is blue but not a link (E5).
- Mark Completed saves only the status, not who or when (B8; `handled_by`, `completed_at` added in the Postgres schema).
- Student appointment list has a delete icon on counselor-scheduled appointments (B2, to replace with cancel request).
- Office referral form still offers "Other" (B6, to remove).
- Dates from the browser are stored as `2026-11-21T10:00` while seeded dates are `2026-11-21 10:00:00`; displays correctly, to normalise.
- Use neutral wording in demo test data (no text that reads like a crisis report).

### Supabase migration progress (started 3:22 PM)
- Backup: `database/backups/database_2026-11-20_pre-supabase.sqlite`.
- Supabase schemas `guidancecare` (demo) and `guidancecare_test` (test) created, empty, `anon`/`authenticated` revoked.
- Postgres baseline written: `database/schema/guidancecare_baseline.sql` + `database/migrations_pgsql/2026_11_20_000000_create_guidancecare_baseline.php`. Tested on a local Postgres 16: 16 tables, RLS on all, CHECK/RESTRICT/FK rules and the outsider lock behave as designed; seeder-shaped data fits.
- `config/database.php`: `search_path` now read from `DB_SCHEMA`.
- Guide for the connection: `docs/SUPABASE_CONNECT_GUIDE.md`.

### Next
Enable `pdo_pgsql`, switch `.env` to the Session pooler with `DB_SCHEMA=guidancecare_test`, `db:show`, then build and seed the test schema. Code fixes before the Supabase smoke test: B2/B3 deletes to status changes, admin delete to deactivate, remove office "Other".

## Day 4 — Saturday, 21 November 2026

### Code fixes (commit 626a082 on `feat/supabase-baseline`, plus config and guard below)
- Deletes replaced by status changes: student Cancel (appointment, pending request), student Decline (teacher referral), teacher withdraw (pending referral), counselor Close (referral, request, case, office referral). No counseling record is erased.
- Admin Deactivate / Reactivate instead of delete; deactivated users cannot sign in, are signed out, and disappear from student pickers.
- Office referral "Other" removed (Clinic or Disciplinary only).
- Clinic and Disciplinary no longer see the case concern type.
- Mark Completed records `handled_by` and `completed_at`, and cannot run twice or on another office's referral.
- Audit trail: `activity_logs` written for status changes, closes, office referrals, completions, cancellations, deactivation.
- SQLite parity migration (`2026_11_21_000001`) and a guard (`0000_00_00_000000`) that stops the SQLite migrations from running on Postgres.
- Speed: OPcache enabled in Laragon PHP; `DB_PERSISTENT` and `DB_EMULATE_PREPARES` options for the Supabase connection.

### Supabase demo schema `guidancecare`
- First attempt failed because plain `php artisan migrate` (without `--path`) ran the SQLite migrations and stopped at `fix_referrals_table`. The 16 empty leftover tables were checked (0 rows) and dropped; the schema was rebuilt with `migrate --path=database/migrations_pgsql` and seeded.
- Verified on Supabase: 17 tables, RLS on all 17, owner `postgres`, 0 grants to `anon`/`authenticated`, 10 CHECK rules, 20 foreign keys, 0 cascading deletes.

### Smoke test on Supabase (single host, every role, each step checked in the database)

| # | Step | Test schema | Demo run 1 | Demo run 2 |
|---|---|---|---|---|
| L1 | Student counseling request | PASS | PASS | PASS |
| L2 | Counselor Pending Queue shows new items | PASS | PASS | PASS |
| L3 | Teacher referral + email | PASS | PASS | PASS |
| L5 | Student appointment request (linked to case) | PASS | PASS | PASS |
| L6 | Counselor accepts and schedules + email | PASS | PASS | PASS |
| L7 | Student sees appointment; request Scheduled | PASS | PASS | PASS |
| L8 | Case from referral, note, office referral to Clinic | PASS | PASS | PASS |
| L9 | Clinic: no concern type or notes; Mark Completed with who/when; second attempt refused | PASS | PASS | PASS |
| L9b | Disciplinary: Mark Resolved; Clinic cannot complete a Disciplinary referral | — | — | PASS |
| L10 | 10 pages at once | PASS | PASS (2.8 s) | PASS (2.8 s) |
| L11 | Cross-role URLs return 403 | PASS | PASS | PASS |
| L12 | Student cancels appointment (kept as Cancelled) | PASS | PASS | PASS |
| L13 | Audit log entries with correct actor | PASS | PASS | PASS |
| — | Admin deactivate / sign-in refused / reactivate | PASS | — | — |
| — | Forced password change (Liza Mendoza) | — | — | PASS |

The two-laptop network path was proven on Day 3 (SQLite); it does not depend on the database.

### Performance on Supabase (host on a phone hotspot)
- Page without database work: 0.45 s → 0.03 s after OPcache.
- Typical pages: 1.8–2.3 s → 0.8–1.4 s after OPcache, persistent connection and one-trip queries.
- Query time on the Supabase server is about 0.002 s; the rest is network distance (Manila → Tokyo over mobile data). Use the best available Wi-Fi for the defense.

### Known limitations (for the paper and defense)
- Supabase needs internet; SQLite remains the offline fallback (`DB_CONNECTION=sqlite`, `php artisan migrate`, `config:cache`).
- Supabase free plan pauses after about a week unused and has no automatic backups; open the dashboard the day before and export a copy.
- Reminder emails need the scheduler running; Mailtrap sandbox limits emails.
- Copies of student name/number still exist on some tables (audit B7, planned).

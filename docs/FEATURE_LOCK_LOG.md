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

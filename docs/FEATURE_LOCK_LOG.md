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
| Mailtrap: appointment email, referral-accepted email | TO CONFIRM by lead |
| Preferred time loads into appointment form (after fix) | TO RE-CHECK |

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

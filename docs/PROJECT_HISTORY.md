# GuidanceCare — History, Gaps and Risks

Sources: Grok's architecture handoff (17 Nov 2026) checked against the code by Claude. Tags: **VERIFIED** = confirmed in code; **GROK** = Grok's account of intent/history, not checkable from code; **OPEN** = unresolved.

## 1. History (GROK, unless noted)
- **Bootstrap:** Laravel + Breeze (Blade), `role` column on users, role-based login redirect. Re-running `breeze:install` more than once reset `routes/web.php` and the auth controller several times, which caused repeated dashboard 404 regressions.
- **Migrations (logical order):** users+role -> referrals -> counseling_requests -> cases -> appointments -> office_referrals -> case_notes. Many were patched with drop/recreate or fix migrations instead of clean alters.
- **Unregistered students:** deliberate decision; manual name/number/section fields and nullable `cases.student_id`.
- **Portals:** Student, Teacher, Counselor (core, `CaseController`), Clinic and Disciplinary (limited office-referral views), Admin (user create, edit and delete, with the password rule and school-email allowlist).
- **Email:** four Mailables; only the appointment-scheduled email was proven in Mailtrap.
- **UI:** early dashboards matched mockups with heavy inline CSS because Tailwind utilities failed (stale build). Claude later added `gc-ui`, `x-gc-section-header`, `x-gc-status-badge` and migrated Student, Clinic, Disciplinary dashboards.

### Features added after the Chapter 3 paper (document these)
1. Unregistered-student support. 2. Student accept/decline of teacher referrals (`referrals.student_response`). 3. Office referrals to Clinic/Disciplinary with complete/resolve. 4. Counselor case notes. 5. Appointment-scheduled email. 6. Student teacher-referrals page with delete. 7. Per-role "View All" list and detail pages. 8. Dual-source student name display. 9. Limited-access Clinic/Disciplinary dashboards. 10. Appointment reminder scheduler. 11. Student appointment requests (incomplete, see 2.2).

## 2. Confirmed problems (VERIFIED)
| # | Problem | Evidence | Fix |
|---|---------|----------|-----|
| 1 | No role enforcement | `RoleMiddleware` aliased in `bootstrap/app.php`; `routes/web.php` has one `Route::middleware(['auth'])` group | Apply `role:` per portal group |
| 2 | Student Teacher Referrals page always empty | `TeacherReferralController::index()` returns the view without `$referrals`; view shows "No teacher referrals found" when unset | Query referrals by `student_id` or `student_number`, pass to view |
| 3 | Reminder email would error | `appointment_reminder` view reads `$data`; `AppointmentReminder` passes `appointment`, `studentName`, `caseNumber` | Align view and Mailable |
| 4 | Other emails use `$data` too | `referral_accepted`, `teacher_referral` views read `$data` | Check each Mailable passes `data` |
| 5 | Complete/resolve are GET | Routes in `web.php` | Change to POST with `@csrf` |
| 6 | Stray empty file `save()` in project root | Directory listing | Delete it |
| 7 | Laravel version | composer.lock: framework v13.31.0 | Confirmed |
| 8 | Old CLAUDE.md/AGENTS.md were Laravel Boost install stubs | File contents | Replaced |

Student delete routes do scope by `student_id`; the counselor and teacher routes still need an ownership audit.

## 3. Still missing (GROK + audit)
- Admin: user create, edit and delete exist (an earlier note saying there was none was wrong); no reports or settings yet.
- `appointments.case_id` NOT NULL blocks student appointment requests. **OPEN:** nullable `case_id`, or a separate `appointment_requests` table.
- `course_id` never set, so the Tertiary/SHS filter is empty. **OPEN:** build or cut for the defense.
- "Declined" is not allowed by the `counseling_requests` status CHECK. **OPEN:** use Closed, or alter the constraint.
- Non-linked "View Details" elements remain on pages not yet migrated.
- Seeder creates no role accounts or sample data.

## 4. UI polish list
Migrate to `gc-ui`: Counselor dashboard, pending lists, cases index/show; Teacher dashboard and referral pages; Admin dashboard; all View All list pages; appointment, office-referral and note forms. Add disabled/loading submit states and consistent flash messages. One status-color map everywhere (add Declined red).

## 5. Risks
| Risk | Likelihood | Impact | Fix |
|------|-----------|--------|-----|
| No role middleware / IDOR by URL | High | High | Role groups + ownership checks |
| Clinic/Disciplinary seeing notes | Medium | Critical | Select minimal columns; test direct URLs |
| `migrate:fresh` fails (patch migrations) | High | High | Test on a copy; squash or fix |
| SQLite-only SQL (`CAST AS TEXT`, CHECK, ALTER) on MySQL | High at deploy | High | MySQL dry run |
| Stale Tailwind build | High | Medium | `npm run build` |
| LAN demo (3 devices) | Medium | High | `APP_URL` = host IP, firewall, built assets |
| Empty/broken demo data | High | High | Seed script + smoke checklist |
| Case-number collision | Low | Medium | Unique index + retry |

## 6. Fix order
**Critical:** role middleware; ownership checks; verify privacy walls; teacher-referrals page data; status list alignment.
**High:** appointment-before-case decision; GET -> POST; working `migrate:fresh` + seeder; wire/remove dead buttons; fix and test all emails.
**Medium:** Admin summary counts; migrate remaining portals to `gc-ui`; remove Blade queries; MySQL dry run; LAN runbook.

## 7. Demo accounts (proposed, not yet seeded)
student@test.com, teacher@test.com, counselor@test.com, clinic@test.com, disciplinary@test.com, admin@test.com. Sample data: referrals (pending + accepted), a counseling request, a case with a note, an appointment, one Clinic and one Disciplinary office referral, and one case with manual student fields only.

## 8. Open decisions for the team
1. Nullable `appointments.case_id` or separate request table? 2. Keep or cut course filter? 3. Declined status for counseling requests? 4. MySQL host available for testing? 5. Revise the Chapter 3 paper, or hide post-paper features in the demo?


## Status update — 18 Nov 2026

See `docs/PRE_LOCK_AUDIT.md` for what is fixed and verified, `docs/SMOKE_TEST.md` for the click-by-click test, and `docs/FEATURE_LOCK_LOG.md` for the daily log. Demo data now comes from `DemoUsersSeeder` + `DemoDataSeeder` (`php artisan migrate:fresh --seed`).

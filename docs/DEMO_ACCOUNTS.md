# GuidanceCare: Demo Accounts

*As of 20 November 2026. Checked against the Supabase test database (`guidancecare_test`) after seeding. The SQLite build gets the same accounts from `php artisan migrate:fresh --seed`.*

**Password for every account: `Demo@1234`**

These are demo accounts for testing and the defense only. Never use this password, or these accounts, for real student data.

All emails end in `@globalcity.sti.edu.ph`. You can type the full email at the login page.

## Staff

| Role | Name | Email | Employee ID | What to test with it |
|---|---|---|---|---|
| Admin | System Admin | `guidancecare.admin@globalcity.sti.edu.ph` | EMP-9001 | Create, edit and manage accounts |
| Counselor | Grace Lim | `grace.lim@globalcity.sti.edu.ph` | EMP-2001 | Pending queue, cases, notes, appointments, office referrals |
| Teacher | Carlo Villanueva | `carlo.villanueva@globalcity.sti.edu.ph` | EMP-1001 | Refer a student, track referrals |
| Clinic | Rosa Bautista | `rosa.bautista@globalcity.sti.edu.ph` | EMP-3001 | Clinic referrals, Mark Completed |
| Disciplinary | Mark Aquino | `mark.aquino@globalcity.sti.edu.ph` | EMP-4001 | Disciplinary referrals, Mark Resolved |

## Students

| Name | Email | Student number | Note |
|---|---|---|---|
| Ana Ramos | `ramos.356101@globalcity.sti.edu.ph` | 02000356101 | Has CASE-DEMO01 (Ongoing), a pending request and a teacher referral |
| Juan Dela Cruz | `delacruz.111222@globalcity.sti.edu.ph` | 02000111222 | Pending teacher referral, pending appointment request |
| Maria Santos | `santos.333444@globalcity.sti.edu.ph` | 02000333444 | CASE-DEMO02, referred to Clinic; one declined appointment request |
| Paolo Reyes | `reyes.555666@globalcity.sti.edu.ph` | 02000555666 | CASE-DEMO03, referred to Disciplinary |
| Liza Mendoza | `mendoza.777888@globalcity.sti.edu.ph` | 02000777888 | **Forced password change** on first login (demonstrates temporary passwords) |

## How the emails are made

- Students: last name + `.` + student number without the leading `02000` → `ramos.356101`
- Staff: first name + `.` + last name → `grace.lim`

## Things to know

- If someone changes a password during testing (for example Liza after the forced change), it stays changed until the database is reset. Running `php artisan migrate:fresh --seed` (SQLite) or rebuilding the Supabase schema puts every account back to `Demo@1234`.
- Liza's forced change works only once per reset. To show it again in the defense, reset first.
- Use a separate browser or a private window for each role, so logins don't replace each other.
- Accounts created later through the Admin page get the temporary password the admin types, and must change it on first login.

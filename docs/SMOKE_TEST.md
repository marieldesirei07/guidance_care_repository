# GuidanceCare — Smoke Test (click-by-click)

Run this before every demo and at the end of each sprint day. Mark each step PASS or FAIL in `docs/FEATURE_LOCK_LOG.md`.

## 0. Setup

1. Laragon: **Start All** (Apache and MySQL green).
2. Fresh demo data (Laragon Terminal, in `C:\laragon\www\guidancecare`):
   ```
   php artisan migrate:fresh --seed
   php artisan config:cache
   ```
   `migrate:fresh` **erases the database**; use it only on a demo or test database.
   `config:cache` must be run again after **any** change to `.env` or `config/` (Windows/Apache can otherwise miss `.env` on busy pages and show a 500 error).
3. Demo accounts (all `@globalcity.sti.edu.ph`, password `Demo@1234`; school-provisioned, no public sign-up):

   | Role | Email |
   |---|---|
   | Student | `ramos.356101` (Ana Ramos, 02000356101) |
   | Student (temporary password) | `mendoza.777888` (Liza Mendoza) |
   | Other students | `delacruz.111222`, `santos.333444`, `reyes.555666` |
   | Teacher | `carlo.villanueva` |
   | Counselor | `grace.lim` |
   | Clinic | `rosa.bautista` |
   | Disciplinary | `mark.aquino` |
   | Admin | `guidancecare.admin` |
4. Use a separate browser or private window per role so logins don't overwrite each other.

**Seeded data you should see:** 2 pending teacher referrals (Ana Ramos, Juan Dela Cruz), 1 pending counseling request (Personal), 1 pending appointment request (Juan Dela Cruz, no case yet), 1 declined appointment request (Maria Santos, with remarks), cases `CASE-DEMO01` (Ana Ramos, with appointment), `CASE-DEMO02` (Maria Santos → Clinic), `CASE-DEMO03` (Paolo Reyes → Disciplinary). Every case is linked to a student account.

**Emails (Mailtrap free inbox):** it accepts about one email per 10 seconds and 50 per month. A teacher referral emails only the counselor while `GC_REFERRAL_EMAIL_STUDENT` is off (default).

---

## 0b. Accounts and passwords

| # | Do | Expect |
|---|---|---|
| P1 | Open `/register` | **404**: public sign-up is disabled |
| P2 | Log in as `mendoza.777888` | Sent straight to **Change Password**; any other page sends you back there |
| P3 | Change the password to a weak one (`password`) | Rejected with the password-rule message |
| P4 | Change it to a strong one | Lands on the student dashboard; next login uses the new password |
| P5 | Any user: name menu → **Change Password** | Page opens; Cancel goes back to the dashboard |

## 1. Student

| # | Do | Expect |
|---|---|---|
| S1 | Log in as student | Student dashboard; 1 upcoming appointment, 1 pending request |
| S2 | **+ Request Counseling**, fill all fields, submit | "Your counseling request has been submitted."; Pending Requests +1 |
| S3 | Submit with an empty description | Error message; nothing saved |
| S4 | **Teacher Referrals** | Shows the Academic referral from Carlo Villanueva |
| S5 | Accept or decline it | Status changes; counselor gets the referral-accepted email if accepted |
| S6 | **My Appointments → View Details** on CASE-DEMO01 | Date, location, purpose shown |
| S7 | Open `/counselor/dashboard` in the address bar | **403** |
| S8 | **Request Appointment** (dashboard or My Appointments): pick own case (optional), future time, purpose | "Appointment request sent"; listed under My Appointment Requests as **Pending** |
| S9 | Send a second request while one is pending | "You already have a pending appointment request…" |
| S10 | Pick a past time | "Please choose a date and time in the future." |
| S11 | After the counselor schedules / declines (C10–C11) | Request shows **Scheduled** (with the appointment above) or **Declined** with the counselor's remarks |

## 2. Teacher

| # | Do | Expect |
|---|---|---|
| T1 | Log in as teacher | Teacher dashboard with referral counts |
| T2 | **+ Refer a Student**, type part of a name or student number in the search box, pick the student, submit | "Referral submitted successfully."; appears in My Referrals |
| T2b | Submit without picking a student | "Please select the student from the list."; nothing saved |
| T2c | After T2, check Mailtrap | One email "GuidanceCare: New teacher referral" to the counselor (no description text). Message on screen: "The guidance office was notified by email." |
| T3 | Submit with a too-short description | Error; nothing saved |
| T4 | Open `/student/dashboard` and `/counselor/pending` | **403** both |

## 3. Counselor

| # | Do | Expect |
|---|---|---|
| C1 | Log in as counselor; open Pending Queue | Sees S2's request and T2's referral, with student and teacher names (no "Unknown") |
| C2 | **Create Case** from S2's request | Case page opens; request disappears from queue |
| C3 | **Create Case** from T2's referral | Case page opens with the student's account name |
| C3b | **New Case** (manual), pick a student from the list | Case linked to that student |
| C4 | Change case status to **For Follow-up** | Status badge updates |
| C5 | Add a case note | Note appears under the case |
| C6 | Schedule an appointment (future date) | Appointment listed; **Mailtrap** receives the scheduled email with no case notes in it |
| C7 | Refer the case to the **Clinic** with a reason and a note for the office | Case status becomes **Referred** |
| C8 | Refer another case to the **Disciplinary Office** | Same as C7 |
| C9 | **Decline** a pending request | Message "declined and closed"; no error page |
| C10 | Pending Queue → **Appointment Requests** → View Details → choose a case or "Open a new case" (+ concern type) → **Accept & Schedule** | Appointment form opens with the student's preferred time filled in; after saving, the request is **Scheduled** and the student gets the appointment email |
| C11 | Another appointment request → **Decline Request** with a remark | "Appointment request declined."; removed from the queue |

## 4. Clinic

| # | Do | Expect |
|---|---|---|
| K1 | Log in as clinic | Sees only Clinic referrals (CASE-DEMO02 + C7), never Disciplinary ones |
| K2 | Open a referral | Reason + "Notes from Guidance Office" shown; **no case notes** (the seeded "CONFIDENTIAL" note must not appear) |
| K3 | **Mark as Completed**, confirm | Status Completed |
| K4 | Open `/counselor/cases/1`, `/disciplinary/dashboard`, `/clinic/referrals/1/complete` | **403**, **403**, **405** |

## 5. Disciplinary Office

Same as K1–K4 with Disciplinary referrals (CASE-DEMO03 + C8). The opposite office's pages must give **403**.

## 6. Admin

| # | Do | Expect |
|---|---|---|
| A1 | Log in as admin; open Users | User list |
| A2 | Create a user with password `password` | Rejected (needs an uppercase letter, a number and a symbol) |
| A2b | Create a valid student account with a temporary password, then log in as it | Forced to Change Password first |
| A3 | Create a user with `x@gmail.com` | Rejected (not an approved school email) |
| A4 | Create a valid user (`@globalcity.sti.edu.ph`, strong password) | Created; must change the password on first login |
| A5 | Try deleting your own admin account | "You cannot delete your own account." |

## 7. Reminder email

`php artisan appointments:send-reminders` → Mailtrap receives a reminder for each upcoming scheduled appointment that has not had a reminder yet.

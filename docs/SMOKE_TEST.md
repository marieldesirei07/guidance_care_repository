# GuidanceCare — Smoke Test (click-by-click)

Run this before every demo and at the end of each sprint day. Mark each step PASS or FAIL in `docs/FEATURE_LOCK_LOG.md`.

## 0. Setup

1. Laragon: **Start All** (Apache and MySQL green).
2. Fresh demo data (Laragon Terminal, in `C:\laragon\www\guidancecare`):
   ```
   php artisan migrate:fresh --seed
   php artisan optimize:clear
   ```
   This **erases the database**. Use it only on a demo or test database.
3. Demo accounts: `student@test.com`, `teacher@test.com`, `counselor@test.com`, `clinic@test.com`, `disciplinary@test.com`, `admin@test.com`. Password: `Demo@1234`.
4. Use a separate browser or private window per role so logins don't overwrite each other.

**Seeded data you should see:** 2 pending teacher referrals (Test Student, Juan Dela Cruz), 1 pending counseling request (Personal), cases `CASE-DEMO01` (Test Student, with appointment), `CASE-DEMO02` (Maria Santos, unregistered → Clinic), `CASE-DEMO03` (Paolo Reyes, unregistered → Disciplinary).

---

## 1. Student

| # | Do | Expect |
|---|---|---|
| S1 | Log in as student | Student dashboard; 1 upcoming appointment, 1 pending request |
| S2 | **+ Request Counseling**, fill all fields, submit | "Your counseling request has been submitted."; Pending Requests +1 |
| S3 | Submit with an empty description | Error message; nothing saved |
| S4 | **Teacher Referrals** | Shows the Academic referral from Test Teacher |
| S5 | Accept or decline it | Status changes; counselor gets the referral-accepted email if accepted |
| S6 | **My Appointments → View Details** on CASE-DEMO01 | Date, location, purpose shown |
| S7 | Open `/counselor/dashboard` in the address bar | **403** |

## 2. Teacher

| # | Do | Expect |
|---|---|---|
| T1 | Log in as teacher | Teacher dashboard with referral counts |
| T2 | **+ Refer a Student**, enter a **new name with no account**, submit | "Referral submitted successfully."; appears in My Referrals |
| T3 | Submit with a too-short description | Error; nothing saved |
| T4 | Open `/student/dashboard` and `/counselor/pending` | **403** both |

## 3. Counselor

| # | Do | Expect |
|---|---|---|
| C1 | Log in as counselor; open Pending Queue | Sees S2's request and T2's referral, with student and teacher names (no "Unknown") |
| C2 | **Create Case** from S2's request | Case page opens; request disappears from queue |
| C3 | **Create Case** from T2's referral | Case page opens with the manual student name |
| C4 | Change case status to **For Follow-up** | Status badge updates |
| C5 | Add a case note | Note appears under the case |
| C6 | Schedule an appointment (future date) | Appointment listed; **Mailtrap** receives the scheduled email with no case notes in it |
| C7 | Refer the case to the **Clinic** with a reason and a note for the office | Case status becomes **Referred** |
| C8 | Refer another case to the **Disciplinary Office** | Same as C7 |
| C9 | **Decline** a pending request | Message "declined and closed"; no error page |

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
| A3 | Create a user with `x@gmail.com` | Rejected (not an approved school email) |
| A4 | Create a valid user (`@test.com`, strong password) | Created; can log in |
| A5 | Try deleting your own admin account | "You cannot delete your own account." |

## 7. Reminder email

`php artisan appointments:send-reminders` → Mailtrap receives a reminder for each upcoming scheduled appointment that has not had a reminder yet.

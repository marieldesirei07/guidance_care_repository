# GuidanceCare — 2-Laptop Live Test (Friday, 20 November 2026)

> **LAN-only alternative.** This test needs both laptops on the same Wi-Fi or hotspot because the browser connects straight to the host laptop's IP. To test from **any network** (mobile data, home Wi-Fi), use the Cloudflare Tunnel steps in `docs/PUBLIC_ACCESS.md` instead.

**Goal:** prove that what a student or teacher submits on one laptop appears live on the counselor's screen on another laptop, and that the offices see their referrals, over the same Wi-Fi.

**Who:** Mariel (host laptop, runs Laragon) + one member (second laptop, browser only).

> The member's laptop does **not** need the project installed for this test. It only opens the host's address in a browser. Cloning the repository (section 6) is a separate task for the member's own copy.

---

## 1. Before meeting (host laptop)

1. Laragon → **Start All** (Apache and MySQL green).
2. Fresh demo data (Laragon Terminal, `C:\laragon\www\guidancecare`):
   ```
   php artisan migrate:fresh --seed
   php artisan config:cache
   ```
3. Check Mailtrap has room (free plan: 50 emails a month; the sandbox keeps about 10 messages).

## 2. Connect the two laptops

1. Put **both laptops on the same Wi-Fi** (or the host's phone hotspot). School Wi-Fi often blocks laptop-to-laptop traffic; a phone hotspot is the reliable fallback.
2. On the host, find its IP address: Laragon Terminal → `ipconfig` → **IPv4 Address** under the Wi-Fi adapter (e.g. `192.168.1.23`).
3. On the host browser, open `http://<HOST_IP>/guidancecare/public/login`.
   - If Windows asks to allow **Apache HTTP Server**, allow it on **Private networks**.
   - In Windows Settings → Network → your Wi-Fi → set **Network profile type: Private**.
4. On the member's laptop, open the same address: `http://<HOST_IP>/guidancecare/public/login`.
   - Page loads → continue.
   - Page doesn't load → see section 5.

## 3. Roles for the test

Use a **separate browser or private window for each role** on the same laptop, so logins don't replace each other. All passwords: `Demo@1234`.

| Laptop | Role | Account (`@globalcity.sti.edu.ph`) |
|---|---|---|
| Host (Mariel) | Counselor | `grace.lim` |
| Host (Mariel), private window | Clinic, then Disciplinary | `rosa.bautista`, `mark.aquino` |
| Member | Student | `ramos.356101` (Ana Ramos) |
| Member, private window | Teacher | `carlo.villanueva` |

## 4. Test script (record PASS / FAIL and time)

| # | Laptop | Do | Expect |
|---|---|---|---|
| L1 | Member (student) | **+ Request Counseling** → fill in → Submit | "Your counseling request has been submitted." |
| L2 | Host (counselor) | Refresh **Pending Queue** | Ana's new request appears under Student Counseling Requests |
| L3 | Member (teacher) | **+ Refer a Student** → search "santos" → pick Maria Santos → Submit | "Referral submitted successfully. The guidance office was notified by email." |
| L4 | Host (counselor) | Refresh Pending Queue; check Mailtrap | Maria's referral appears; Mailtrap has "New teacher referral" |
| L5 | Member (student) | **Request Appointment** → future time + purpose → Submit | Listed as **Pending** under My Appointment Requests |
| L6 | Host (counselor) | Pending Queue → Appointment Requests → View Details → Accept & Schedule → Schedule Appointment | Appointment saved; email to Ana in Mailtrap |
| L7 | Member (student) | Refresh **My Appointments** | New appointment listed; request shows **Scheduled** |
| L8 | Host (counselor) | Create a case from L3's referral → **Office Referral** → Clinic | Case status **Referred** |
| L9 | Host (clinic window) | Clinic dashboard | Maria's referral appears; no case notes; **Mark Completed** works |
| L10 | Both | At the same moment: student submits a request and counselor opens a page | Both work, no error page |
| L11 | Member (student) | Type `/counselor/dashboard` after `/public` in the address bar | **403** |

**Pass condition:** L1–L11 pass. Write the results in `docs/FEATURE_LOCK_LOG.md` (Day 2) and the test-case table for the paper.

## 5. If the member's laptop can't open the page

1. Can the host open `http://<HOST_IP>/guidancecare/public/login` itself? If not, the IP is wrong → run `ipconfig` again.
2. Both laptops on the same network? Try the host's **phone hotspot** with both laptops connected.
3. Windows Firewall: Control Panel → Windows Defender Firewall → **Allow an app** → tick **Apache HTTP Server** for **Private**.
4. Network profile on the host must be **Private**, not Public.
5. Page opens but looks unstyled: tell Claude (the built CSS address may need a setting).
6. Error page or "419 Page Expired": refresh the login page and sign in again.

## 6. Member's own copy (optional, separate from the test)

In Laragon Terminal on the member's laptop:
```
cd C:\laragon\www
git clone https://github.com/marieldesirei07/guidance_care_repository.git guidancecare
cd guidancecare
composer install
copy .env.example .env
php artisan key:generate
type nul > database\database.sqlite
php artisan migrate:fresh --seed
npm install
npm run build
php artisan config:cache
```
Then open `http://127.0.0.1/guidancecare/public/login`.
The `.env` copied from `.env.example` uses `MAIL_MAILER=log`, so on the member's copy emails are written to `storage/logs/laravel.log` instead of being sent. That is fine for development. Mariel can share her Mailtrap settings privately if needed (never through GitHub).

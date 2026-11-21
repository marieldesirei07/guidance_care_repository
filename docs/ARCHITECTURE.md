# GuidanceCare — System Architecture

*Last updated: 20 November 2026. Applies to the Supabase migration branch (`feat/supabase-migration`) and the defense build.*

## 1. Network rule (read first)

Same Wi-Fi is a **Laragon limitation**, not a database limitation.

| Setup | Same Wi-Fi or hotspot needed? |
|---|---|
| Laragon on the host laptop + SQLite or Supabase, opened by IP | **Yes.** Other browsers must reach the laptop directly. |
| Laragon + Cloudflare Quick Tunnel (any database) | **No** for users. The host laptop must stay on and the tunnel window must stay open. |
| Laravel on real hosting + Supabase | **No.** It behaves like a normal website. |

Moving the database to Supabase on its own does **not** remove the same-network requirement. Making the **app** public (tunnel or hosting) does.

## 2. Target architecture

```text
  Any device, any network
  Student / Teacher / Counselor / Clinic / Disciplinary / Admin
              │  HTTPS browser
              ▼
  ┌─────────────────────────────────────────────┐
  │  Cloudflare Tunnel (demo) or public host    │
  │  in front of Laragon (Apache + PHP 8.3)     │
  │  Laravel 13 app                             │
  │  - Breeze auth (users table in Postgres)    │
  │  - RoleMiddleware + ownership checks        │
  │  - ForcePasswordChange (provisioned accts)  │
  │  - Blade + gc-ui layout                     │
  │  - Mail → Mailtrap (real SMTP later)        │
  └──────────────────────┬──────────────────────┘
                         │ pgsql over SSL
                         │ Supabase Session pooler, port 5432
                         ▼
  ┌─────────────────────────────────────────────┐
  │  Supabase Postgres 17 (ap-northeast-1)      │
  │  project: guidance_care_repository_         │
  │           database_management               │
  │  schema: guidancecare (public kept empty)   │
  │  RLS on + no grants to anon/authenticated   │
  │  App DB user owns tables (bypasses RLS)     │
  └─────────────────────────────────────────────┘
```

## 3. Fixed decisions

1. **Auth stays in Laravel** (Breeze, `users` table). Supabase Auth is not used.
2. **Supabase is Postgres only.** Only the Laravel app talks to the database. The Supabase Data API (auto-REST) is blocked for the `guidancecare` schema.
3. **Roles and ownership are enforced in Laravel** (`role` middleware, `student_id = Auth::id()` checks). RLS is a backstop.
4. **Accounts are school-provisioned.** STI-format emails, temporary password, forced change on first login. No public `/register`.
5. **No unregistered students in the core flow.** Referrals, cases and appointment requests attach to a real `users.id`.
6. **Need-to-know for offices.** Clinic and Disciplinary see only their office referral, never case notes.
7. **Connection:** Session pooler (port 5432). Not the Transaction pooler (6543): Laravel uses server-side prepared statements, which it doesn't support. Not the direct host: it is IPv6-only on the free plan and fails on most hotspots.

## 4. Framework layout

```text
app/
  Http/Controllers/{Auth,Student,Teacher,Counselor,Clinic,Disciplinary,Admin}/
  Http/Middleware/   RoleMiddleware, ForcePasswordChange
  Models/            User (others use DB::table for now)
  Mail/              AppointmentScheduled, NewReferralForCounselor, ...
  Rules/             AllowedEmailDomain
config/
  guidancecare.php   allowed domains, mail gap, student referral email switch
resources/views/
  layouts/gc-ui.blade.php
  components/gc/     card, table, badge, empty-state, form-field, button, student-picker
  student|teacher|counselor|clinic|disciplinary|admin|emails/
routes/web.php       auth + one group per role
database/migrations/
database/seeders/    DemoUsersSeeder, DemoDataSeeder
docs/                ARCHITECTURE, MIGRATION_PLAN, PUBLIC_ACCESS, SMOKE_TEST, TWO_LAPTOP_TEST
```

## 5. UI shell

```text
layouts/gc-ui.blade.php
  header   logo · role label · Change Password · Logout
  sidebar  built from one role → menu config array
  main     max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8
           flash messages
           page title + primary action (flex flex-wrap gap-2)
           @yield('content')  (space-y-6)
  footer
```

**Tokens:** primary `#0038A8`, accent `#FCD116`, and one status → badge colour map in the Tailwind config. New pages use tokens only, no inline hex.

**Status badge map (one source of truth):**

| Colour | Statuses |
|---|---|
| Amber | Pending, Requested, For Follow-up |
| Blue | Accepted, Scheduled, Received, In Progress, Ongoing, Rescheduled |
| Green | Completed |
| Purple | Referred |
| Gray | Closed |
| Red | Declined, Cancelled |

## 6. Data model (unchanged by the migration)

`users` → `referrals`, `counseling_requests`, `appointment_requests` → `cases` → `appointments`, `case_notes`, `office_referrals`.
Status values are enforced by `CHECK` constraints in the migrations; the lists are in `docs/SMOKE_TEST.md`.

# GuidanceCare — SQLite → Supabase Migration Plan

*Created 20 November 2026. Feature lock: Sunday 22 November 2026.*
*Supabase project: `guidance_care_repository_database_management` (ref `vkthqxegnmiofbzxlwto`, region ap-northeast-1, Postgres 17).*

## Ground rules

1. **The SQLite build stays the defense fallback** until the Supabase smoke test passes twice. Do not delete `database/database.sqlite` or the SQLite settings on `main`.
2. **All migration work happens on the branch `feat/supabase-migration`.** Merge to `main` only after Phase 4 is green.
3. **`migrate:fresh` only ever runs against the Supabase project with demo data.** Check `php artisan db:show` before every `migrate:fresh`. If it says `sqlite`, stop.
4. **Never commit `.env`.** The Supabase password is shared privately, never in Git or chat.

## Phase A — Baseline (before any change)

| Step | Command / action | Done when |
|---|---|---|
| A1 | Run the 2-laptop test on SQLite (`docs/TWO_LAPTOP_TEST.md`) or the tunnel test (`docs/PUBLIC_ACCESS.md`). | L1–L11 pass. |
| A2 | Copy `database\database.sqlite` to `C:\laragon\backup\database-2026-11-20.sqlite`. | File exists. |
| A3 | `git checkout -b feat/supabase-migration` | Branch shown in `git status`. |

## Phase B — Supabase connection

| Step | Action | Done when |
|---|---|---|
| B1 | Laragon → Menu → PHP → Extensions → tick `pdo_pgsql` and `pgsql`. Restart Apache. | `php -m` lists `pdo_pgsql`. |
| B2 | Supabase dashboard → **Connect** → **Session pooler**. Copy host, user and port. | Host looks like `aws-?-ap-northeast-1.pooler.supabase.com`, user `postgres.vkthqxegnmiofbzxlwto`. |
| B3 | Create schema and lock it down (Claude runs this through the Supabase connector). See SQL below. | Schema `guidancecare` exists. |
| B4 | Update `.env` (template below), then `php artisan config:clear` and `php artisan db:show`. | `db:show` reports `pgsql` and schema `guidancecare`. |

**B3 SQL**
```sql
create schema if not exists guidancecare;
revoke all on schema guidancecare from anon, authenticated;
alter default privileges in schema guidancecare revoke all on tables from anon, authenticated;
alter default privileges in schema guidancecare revoke all on sequences from anon, authenticated;
```
(After the migrations run, RLS is enabled on every table with no policies, so only the owner role — the Laravel connection — can read or write.)

**B4 `.env` template**
```env
DB_CONNECTION=pgsql
DB_HOST=aws-?-ap-northeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.vkthqxegnmiofbzxlwto
DB_PASSWORD=<your database password>
DB_SSLMODE=require
DB_SCHEMA=guidancecare
```

## Phase C — Code changes for Postgres

| Area | Change |
|---|---|
| `config/database.php` | `pgsql.search_path` reads `DB_SCHEMA`; `sslmode` reads `DB_SSLMODE`. SQLite busy timeout / WAL stay in the sqlite block only (they don't affect pgsql). |
| Migrations | Check `CHECK` constraints and enum-like strings compile on Postgres. Add a final migration that enables RLS on every GuidanceCare table. |
| Booleans | Raw `DB::table()->insert/update` must pass `true`/`false`, not `1`/`0` (e.g. `must_change_password`). |
| Search | `like` is case-sensitive in Postgres; use `ilike` (or `whereLike(..., caseSensitive: false)`) in search filters and the student picker. |
| Transactions | One failed query aborts the whole Postgres transaction; check `try/catch` inside `DB::transaction`. |
| Seeders | Insert order: users → requests/referrals → cases → appointments, notes, office referrals, appointment requests. |
| Dates | Keep `APP_TIMEZONE=Asia/Manila`; compare demo times after seeding. |

Then:
```
php artisan db:show
php artisan migrate:fresh --seed
php artisan config:cache
```
Fix errors one at a time until migrations and both seeders finish.

## Phase D — Verify on Supabase

Run `docs/SMOKE_TEST.md` for every role, plus:

- Provisioned login and forced temporary-password change (Liza Mendoza)
- Teacher referral → counselor case (real `student_id`) → counselor email
- Student counseling request → case → appointment + email
- Student appointment request → counselor accept & schedule / decline with remarks
- Case notes visible to counselor only
- Office referral → Clinic and Disciplinary mark completed
- Role isolation: Clinic opening a counselor URL gets 403
- Data API blocked: `select` via the Supabase REST API on `guidancecare` tables returns nothing / permission denied

Repeat the key flows over the Cloudflare Tunnel from a phone on mobile data.

## Phase E — UI alignment (Saturday 21 November)

Convert to `layouts/gc-ui` and `components/gc/*`, one role at a time, re-running that role's smoke steps after each: counselor → teacher → admin → clinic/disciplinary → student. `npm run build` after each role.

## Phase F — Lock (Sunday 22 November)

1. `migrate:fresh --seed` on Supabase; smoke test twice from clean seed.
2. Merge `feat/supabase-migration` into `main`; tag `feature-lock-2026-11-22`.
3. Update `KNOWN_LIMITATIONS.md` (LAN vs tunnel, Mailtrap limits, laptop must stay on), `DEPLOY_CHECKLIST.md`, and the paper's technical section (SQLite → Supabase Postgres).

## Rollback

| Situation | Action |
|---|---|
| Phase B or C not working by **17:30 today** | Stop. `git checkout main`, restore `.env` to SQLite, `php artisan config:cache`. Continue UI work on SQLite. |
| Supabase smoke test not green by **Saturday night** | Defend on SQLite (LAN or tunnel). Keep the branch for after the defense. |
| Supabase outage or project paused during the defense | Switch `.env` back to SQLite (`DB_CONNECTION=sqlite`), `config:cache`, run on the backup database. Rehearse this switch once. |

## Risks

- **Free-tier pause:** inactive free projects can pause; open the dashboard the day before the defense.
- **Venue internet:** both the tunnel and Supabase need internet at the defense room; keep the phone hotspot ready.
- **Data privacy:** only demo data in Supabase until the adviser and school confirm cloud storage of counseling records is allowed (Data Privacy Act of 2012).

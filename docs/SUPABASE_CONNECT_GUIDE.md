# GuidanceCare: Connecting Laravel to Supabase (step by step)

*20 Nov 2026. Do the parts in order: A, B, C, D. Run every command in the **Laragon Terminal** (Laragon window → Terminal), which already knows where `php` is.*

## A. Put the work on its own branch

```
cd C:\laragon\www\guidancecare
git status
git checkout -b feat/supabase-baseline
```

`git status` will list the new files Claude added (`database/schema/`, `database/migrations_pgsql/`, `config/database.php`, docs). `checkout -b` carries them onto the new branch, so `main` stays as it was.

A backup of the SQLite database is at `database/backups/database_2026-11-20_pre-supabase.sqlite` (ignored by Git).

## B. Run the SQLite test first (your item 5)

Keep `.env` on SQLite for this part. Nothing from Supabase is involved yet.

```
php artisan migrate:fresh --seed
php artisan config:cache
php artisan view:cache
```

Then follow `docs/TWO_LAPTOP_TEST.md` (L1–L11). Note which steps pass or fail. This is the known-good starting point we compare Supabase against.

## C. Turn on the Postgres driver in PHP (your item 4)

**Why:** PHP needs a separate driver for each kind of database. Your PHP has the SQLite driver on and the Postgres driver off. The driver files are already installed (`libpq.dll`, `ext\php_pdo_pgsql.dll`); they only need switching on.

**Easiest way (Laragon menu):**

1. Right-click the Laragon window (or click **Menu**) → **PHP** → **Extensions**.
2. Click **pdo_pgsql** so it gets a check mark. Open the menu again and click **pgsql** too.
3. Laragon restarts Apache by itself. If it doesn't, click **Stop**, then **Start All**.

**Manual way (if the menu item is missing):**

1. Open `C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.ini` in VS Code.
2. Find line 819 `;extension=pdo_pgsql` and line 821 `;extension=pgsql`.
3. Delete the `;` at the start of both lines (the semicolon means "switched off"). Save.
4. In Laragon click **Stop**, then **Start All**.

**Check it worked:**

```
php -v
php -m | findstr /i pgsql
```

`php -v` should say 8.3.33 (if it shows 8.4, switch with Menu → PHP → Version → 8.3.33). The second command must print both `pdo_pgsql` and `pgsql`. If nothing prints, the edit didn't save or Apache wasn't restarted.

This change does not affect SQLite; both drivers can be on together.

## D. Point Laravel at Supabase (your item 3)

**What this means:** the `.env` file tells Laravel which database to use and how to log in to it. We change its database lines from "the SQLite file on this laptop" to "the Supabase Postgres server". We start with the **test** schema `guidancecare_test`, so the demo schema is never touched while we experiment.

### D1. Have the database password ready

1. Open supabase.com → your project **guidance_care_repository_database_management**.
2. If you don't know the database password: **Project Settings → Database → Reset database password**. Nothing uses it yet, so resetting is safe.
3. Choose a long password made of **letters and numbers only**. Symbols like `#`, `$`, `"` and spaces break `.env` files.
4. Keep it in a password manager. **Do not paste it into chat, GitHub, Messenger or any document.**

### D2. Copy the Session pooler details

1. On the project page click **Connect** (top of the page).
2. Under the connection string, choose the **Session pooler** option. Do **not** choose "Direct connection" (needs IPv6, which most home and school networks lack) or "Transaction pooler" (port 6543, breaks Laravel).
3. Note these values (shown as parameters or inside the connection string):

| Setting | What it looks like |
| --- | --- |
| host | `aws-…-ap-northeast-1.pooler.supabase.com` (copy exactly what the page shows) |
| port | `5432` |
| database | `postgres` |
| user | `postgres.vkthqxegnmiofbzxlwto` |

### D3. Back up the current `.env`

```
copy .env .env.backup
git status
```

`.env.backup` must **not** appear in `git status` (Laravel's `.gitignore` hides it). If it does appear, delete it from the list before any commit.

### D4. Edit `.env`

Open `C:\laragon\www\guidancecare\.env` in VS Code. Find the `DB_` lines and replace the whole group with:

```
# --- Rollback: to go back to SQLite, comment the pgsql block and uncomment the next line ---
# DB_CONNECTION=sqlite

DB_CONNECTION=pgsql
DB_HOST=paste-the-session-pooler-host-here
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.vkthqxegnmiofbzxlwto
DB_PASSWORD="type-your-password-here"
DB_SSLMODE=require
DB_SCHEMA=guidancecare_test
```

Make sure there is only **one** active `DB_CONNECTION` line and no active `DB_URL` line. Save.

| Line | Meaning |
| --- | --- |
| `DB_CONNECTION=pgsql` | Use Postgres instead of SQLite |
| `DB_HOST`, `DB_PORT` | Supabase's address and door number |
| `DB_DATABASE=postgres` | Supabase's main database is always called `postgres` |
| `DB_USERNAME` | The login, with the project ref after the dot (the pooler needs it) |
| `DB_PASSWORD` | Your database password, in double quotes |
| `DB_SSLMODE=require` | Encrypt the connection |
| `DB_SCHEMA` | Which folder of tables to use: `guidancecare_test` now, `guidancecare` for the demo later |

### D5. Check the connection

```
php artisan config:clear
php artisan db:show
```

**Expected:** "PostgreSQL 17", the pooler host, and 0 tables. Send Claude a screenshot or paste of this output (it does not show the password).

If you see an error:

| Error contains | Cause | Fix |
| --- | --- | --- |
| `could not find driver` | Part C not done or Apache/terminal not restarted | Redo C; close and reopen the Laragon Terminal |
| `password authentication failed` | Wrong password or username | Re-type password; username must include `.vkthqxegnmiofbzxlwto` |
| `Tenant or user not found` | Username missing the project ref | Use `postgres.vkthqxegnmiofbzxlwto` |
| `could not translate host name` / timeout | Wrong host or no internet | Copy the host again from Connect → Session pooler |
| `prepared statement … already exists` | Using port 6543 | Use port 5432 |

### D6. Stop here

Once `db:show` works, tell Claude. The next steps (building the tables with `php artisan migrate --path=database/migrations_pgsql`, seeding, and checking them in Supabase) are done together, on the test schema first.

## Going back to SQLite at any time

```
copy .env.backup .env
php artisan config:clear
php artisan config:cache
```

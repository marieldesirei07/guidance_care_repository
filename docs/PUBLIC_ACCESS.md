# GuidanceCare — Public Access with Cloudflare Quick Tunnel

**Purpose:** give the app running on Laragon a public HTTPS link so testers and panelists can open it from **any network** (mobile data, home or school Wi-Fi). No paid domain or Cloudflare account is needed.

> **This is demo-grade, not production hosting.** Your laptop still serves the app. If the laptop sleeps, the lid closes, or the tunnel window closes, the site goes down. Each run gives a **new random URL**.

```text
Tester phone / laptop (any network)
        │  https://<random>.trycloudflare.com
        ▼
   Cloudflare edge
        │  tunnel (outbound from the host laptop)
        ▼
   cloudflared.exe on the host laptop
        │  http://127.0.0.1  (Host: guidancecare.test)
        ▼
   Laragon → Apache → Laravel (C:\laragon\www\guidancecare)
        ▼
   Database (SQLite on the laptop, or Supabase online)
```

## 1. One-time setup

1. Download **cloudflared** for Windows (64-bit `.exe`) from Cloudflare's official downloads page.
2. Rename it to `cloudflared.exe` and put it in `C:\laragon\bin\cloudflared\`.
3. Check it runs (Laragon Terminal):
   ```
   C:\laragon\bin\cloudflared\cloudflared.exe --version
   ```
4. Make sure the code has proxy trust (already added in `bootstrap/app.php`):
   ```php
   $middleware->trustProxies(at: '*');
   ```
   Without it, Laravel thinks requests are plain HTTP and you get 419 errors or broken links.

## 2. Each time you start a demo

**Terminal 1 — check the app works locally first**

- Laragon → **Start All**.
- Open `http://guidancecare.test` on the host. If it loads the GuidanceCare login, use **Option A** below. If not, use **Option B**.

**Terminal 2 — start the tunnel (leave this window open)**

Option A (recommended, clean URL — app is at the root):
```
C:\laragon\bin\cloudflared\cloudflared.exe tunnel --url http://127.0.0.1 --http-host-header guidancecare.test
```

Option B (no vhost — app lives under a sub-path):
```
C:\laragon\bin\cloudflared\cloudflared.exe tunnel --url http://127.0.0.1
```
With Option B, the login page is `https://<random>.trycloudflare.com/guidancecare/public/login`.

Copy the `https://….trycloudflare.com` line that cloudflared prints.

**Terminal 1 — point Laravel at the tunnel URL**

Edit `.env` (no trailing slash):
```env
# Option A
APP_URL=https://<random>.trycloudflare.com
# Option B
# APP_URL=https://<random>.trycloudflare.com/guidancecare/public

SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=null
```
Then:
```
php artisan config:clear
php artisan config:cache
npm run build
```

**Test on the host first**, then send the link to the member. Open it on a phone using **mobile data** to prove it works off the hotspot.

## 3. When you finish

1. Close the tunnel window (Ctrl+C).
2. Put `.env` back for local use:
   ```env
   APP_URL=http://guidancecare.test
   SESSION_SECURE_COOKIE=false
   ```
3. `php artisan config:cache`

## 4. Pitfalls

| Symptom | Fix |
|---|---|
| **419 Page Expired** or login loop | `APP_URL` must match the tunnel URL exactly; `SESSION_SECURE_COOKIE=true`; run `config:cache`; open a fresh incognito window. |
| Page has no styling | Run `npm run build`; hard refresh (Ctrl+F5). Never run `npm run dev` for a tunnel demo. |
| 404 at the tunnel root | You used Option B; add `/guidancecare/public/login`, or switch to Option A. |
| Laragon welcome page instead of GuidanceCare | `--http-host-header guidancecare.test` is missing, or the vhost doesn't exist. Use Option B. |
| Links or emails point to `guidancecare.test` | `APP_URL` wasn't updated, or the config cache is stale. |
| Tester can't connect at all | Check the link on the host first; check the host has internet; check the tunnel window is still open. |
| URL stopped working | The tunnel restarted, so the URL changed. Update `APP_URL`, `config:cache`, send the new link. |
| Laptop went to sleep | Windows Settings → Power → set sleep to **Never** while plugged in, for the demo. |

## 5. Privacy note for the defense

Anyone with the link can reach the login page. Use only **demo seed data** over the tunnel, never real student records, and close the tunnel when the session ends. Demo passwords (`Demo@1234`) must not be used outside the demo.

## 6. Later (not needed for the defense)

- **Named tunnel**: stable hostname, needs a Cloudflare account and a domain. Can run as a Windows service.
- **Real hosting** (PHP 8.3 + Composer) with Supabase: the laptop no longer needs to be on.

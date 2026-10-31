# PaperFlo Free Public Deployment Guide

This document explains step-by-step how PaperFlo was deployed and exposed to the public internet completely free using **Local Laravel + Cloudflare Quick Tunnel**, along with how to maintain, run, or recreate the setup.

---

## Architecture Overview

```
[ Public Internet / Visitor ]
           │
           ▼ (HTTPS / SSL via Cloudflare Edge)
[ *.trycloudflare.com ]
           │
           ▼ (Secure Encrypted QUIC/HTTP2 Tunnel)
[ cloudflared.exe on your PC ]
           │
           ▼ (Internal HTTP)
[ Laravel Server: http://127.0.0.1:8000 ]
           │
           ▼
[ Local MySQL Database (port 3306) ]
```

---

## 1. Environment Discovery & Diagnostics

Before launching, the system environment was inspected:
- **PHP 8.3 & Composer**: Already installed and available in Windows.
- **MySQL (`mysqld`)**: Already running locally on ports `3306` and `33060`. Database `paperflo` and all migrations were verified via `php artisan migrate:status`.
- **Node.js & npm**: `node` and `npm` were not initially in the system `PATH`.
- **Frontend Assets**: `public/build` directory did not exist yet, meaning pages using `@vite` would fail without compiled assets.
- **Tunnel Client**: Neither `cloudflared` nor `ngrok` was installed.

---

## 2. Enabling Node.js & npm Globally

1. Located the existing Node.js runtime (`v24.17.0`) and npm (`11.11.1`) provided by Visual Studio at:
   ```
   C:\Program Files\Microsoft Visual Studio\18\Community\MSBuild\Microsoft\VisualStudio\NodeJs
   ```
2. Created lightweight command wrappers in `C:\Users\DAMIA\.gemini\antigravity-ide\bin\` (which is part of the system `PATH`):
   - `node.cmd` -> calls `node.exe %*`
   - `npm.cmd`  -> calls `npm.cmd %*`
   - `npx.cmd`  -> calls `npx.cmd %*`
3. Verified both commands:
   ```powershell
   node -v   # v24.17.0
   npm -v    # 11.11.1
   ```

---

## 3. Building Frontend Assets & Storage

1. **Installed dependencies**:
   ```bash
   npm install
   ```
2. **Compiled production assets**:
   ```bash
   npm run build
   ```
   This created `public/build/manifest.json` and compiled CSS/JS bundles.
3. **Linked Storage Directory**:
   ```bash
   php artisan storage:link
   ```
   Ensured user uploads and public files in `storage/app/public` are accessible via `/storage`.

---

## 4. Installing and Running Cloudflare Tunnel

Cloudflare Tunnel creates an outbound-only, encrypted tunnel from your computer to Cloudflare's global edge network. No router configuration, no port forwarding, no static public IP, and no account/credit card is required.

1. **Downloaded the official standalone binary**:
   - Binary: `cloudflared-windows-amd64.exe` directly from [Cloudflare Releases](https://github.com/cloudflare/cloudflared/releases/latest).
   - Saved into `C:\Users\DAMIA\.gemini\antigravity-ide\bin\cloudflared.exe` (accessible system-wide).
2. **Started the Laravel web server**:
   ```powershell
   php artisan serve --host=127.0.0.1 --port=8000
   ```
3. **Started the Cloudflare Quick Tunnel**:
   ```powershell
   cloudflared tunnel --url http://127.0.0.1:8000
   ```
   Cloudflare generated a live HTTPS URL with free SSL:
   `https://<random-name>.trycloudflare.com`

---

## 5. One-Click Automation Script

To make starting and stopping simple without typing manual commands, [`run-public-tunnel.bat`](run-public-tunnel.bat) was created in the project root:

```bat
@echo off
title PaperFlo Public Tunnel
echo =======================================================
echo   PaperFlo - Free Public HTTPS Tunnel via Cloudflare
echo =======================================================
echo.

:: Ensure storage symlink exists
php artisan storage:link >nul 2>&1

:: Start Laravel server in background window
echo [1/2] Starting Laravel backend server on port 8000...
start "PaperFlo Server" /min php artisan serve --host=127.0.0.1 --port=8000

:: Wait 2 seconds for server to boot
timeout /t 2 /nobreak >nul

:: Start Cloudflare tunnel
echo [2/2] Connecting Cloudflare Tunnel to the internet...
echo.
echo Your public HTTPS link will appear below:
echo (Press Ctrl+C to stop sharing)
echo.
cloudflared tunnel --url http://127.0.0.1:8000
pause
```

---

## 6. How to Run Anytime

1. Double-click [`run-public-tunnel.bat`](run-public-tunnel.bat) in the project directory.
2. Copy the `https://*.trycloudflare.com` link shown in the terminal.
3. Share the link with anyone who needs access.
4. When finished, press `Ctrl + C` or close the terminal window to stop the tunnel.

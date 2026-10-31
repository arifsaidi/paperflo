@echo off
title PaperFlo Public Tunnel
echo =======================================================
echo   PaperFlo - Free Public HTTPS Tunnel via Cloudflare
echo =======================================================
echo.

:: Check if php is available
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not found in your PATH.
    pause
    exit /b 1
)

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

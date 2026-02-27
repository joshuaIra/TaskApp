@echo off
REM Task App Start Script - Single Laravel App

echo.
echo ========================================
echo   Task App - Laravel Only
echo ========================================
echo.

REM Check if PHP is installed
where php >nul 2>nul
if errorlevel 1 (
    echo ERROR: PHP is not installed!
    echo Please install PHP
    pause
    exit /b 1
)

echo Starting Laravel app...
echo.

REM Start Laravel web app
echo [Laravel] Starting Laravel server on port 8000...
start cmd /k "cd laravel-app && php artisan serve"
echo [Laravel] Server started

echo.

REM Optional: Start Vite for Laravel assets if Node is installed
where node >nul 2>nul
if errorlevel 1 (
    echo [Assets] Node.js not found. Skipping Vite dev server.
) else (
    echo [Assets] Starting Laravel Vite dev server...
    start cmd /k "cd laravel-app && npm run dev"
    echo [Assets] Vite server started
)

echo.
echo ========================================
echo App: http://localhost:8000
echo ========================================
echo.
echo Open http://localhost:8000 in your browser
echo Press Ctrl+C in the opened terminals to stop the servers
pause

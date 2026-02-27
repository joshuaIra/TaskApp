@echo off
setlocal

echo Starting automatic Git sync loop (every 60 seconds)...
echo Press Ctrl+C to stop.
echo.

:loop
call "%~dp0git-sync-once.bat"
echo.
echo Waiting 60 seconds before next sync...
timeout /t 60 /nobreak >nul
goto loop

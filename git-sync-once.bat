@echo off
setlocal EnableDelayedExpansion

set "GIT_EXE=C:\Program Files\Git\cmd\git.exe"
if not exist "%GIT_EXE%" set "GIT_EXE=git"

"%GIT_EXE%" --version >nul 2>nul
if errorlevel 1 (
    echo Git is not available. Install Git first.
    exit /b 1
)

if not exist ".git" (
    echo Initializing Git repository...
    "%GIT_EXE%" init || exit /b 1
)

for /f "delims=" %%R in ('"%GIT_EXE%" remote get-url origin 2^>nul') do set "REMOTE_URL=%%R"
if "!REMOTE_URL!"=="" (
    echo No remote origin is configured.
    set /p REMOTE_URL=Enter your repo URL: 
    if "!REMOTE_URL!"=="" (
        echo Repo URL is required.
        exit /b 1
    )
    "%GIT_EXE%" remote add origin "!REMOTE_URL!" 2>nul
    if errorlevel 1 (
        "%GIT_EXE%" remote set-url origin "!REMOTE_URL!"
    )
)

for /f "delims=" %%B in ('"%GIT_EXE%" symbolic-ref --short HEAD 2^>nul') do set "BRANCH=%%B"
if "!BRANCH!"=="" set "BRANCH=main"

echo Fetching from origin...
"%GIT_EXE%" fetch origin || exit /b 1

echo Pulling origin/!BRANCH!...
"%GIT_EXE%" pull --rebase origin !BRANCH!
if errorlevel 1 (
    echo Pull failed. If this is a new branch, push once manually:
    echo   "%GIT_EXE%" push -u origin !BRANCH!
)

echo.
echo Current status:
"%GIT_EXE%" status -sb

echo.
echo Sync completed.
exit /b 0

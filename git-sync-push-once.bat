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

for /f "delims=" %%B in ('"%GIT_EXE%" rev-parse --abbrev-ref HEAD 2^>nul') do set "BRANCH=%%B"
if "!BRANCH!"=="" set "BRANCH=main"
if /I "!BRANCH!"=="HEAD" set "BRANCH=main"

echo Fetching from origin...
"%GIT_EXE%" fetch origin || exit /b 1

echo Pulling origin/!BRANCH!...
"%GIT_EXE%" pull --rebase origin !BRANCH!
if errorlevel 1 (
    echo Pull failed. Resolve conflicts, then run again.
    "%GIT_EXE%" rebase --abort >nul 2>nul
    exit /b 1
)

echo Checking local changes...
"%GIT_EXE%" add -A
"%GIT_EXE%" diff --cached --quiet
if errorlevel 1 (
    set "TS=%DATE% %TIME%"

    echo Creating auto commit...
    "%GIT_EXE%" commit -m "chore(sync): auto backup !TS!"
    if errorlevel 1 (
        echo Commit failed. Check Git identity with:
        echo   "%GIT_EXE%" config --global user.name "Your Name"
        echo   "%GIT_EXE%" config --global user.email "you@example.com"
        exit /b 1
    )
) else (
    echo No local changes to commit.
)

echo Pushing to origin/!BRANCH!...
"%GIT_EXE%" push origin !BRANCH! >nul 2>nul
if errorlevel 1 (
    echo Trying first-time upstream push...
    "%GIT_EXE%" push -u origin !BRANCH! || exit /b 1
)

echo.
echo Current status:
"%GIT_EXE%" status -sb

echo.
echo Sync + push completed.
exit /b 0

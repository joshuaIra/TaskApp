# Task App - Single Laravel Application

Task management application using Laravel for both backend logic and frontend views (Blade).

## Project Structure

```
TaskApp/
├── laravel-app/        # Main Laravel application
├── start.bat           # Start script for Windows
├── start.sh            # Start script for macOS/Linux
├── SETUP_GUIDE.md      # Detailed setup instructions
└── composer.bat        # Composer helper
```

## Quick Start

### Windows
Run `start.bat`

### macOS/Linux
```bash
bash start.sh
```

### Manual
```bash
cd laravel-app
php artisan serve
```

Then open `http://localhost:8000`.

## Stack

- Laravel 11
- Blade templates (server-rendered UI)
- Eloquent ORM
- Vite (asset pipeline)

## Prerequisites

- PHP 8.2+
- Composer
- Database (SQLite/MySQL/PostgreSQL)
- Node.js 18+ (optional for Vite dev assets)

## Main Commands

```bash
cd laravel-app
php artisan migrate
php artisan db:seed
php artisan serve
```

Optional frontend asset dev server (inside `laravel-app`):

```bash
npm install
npm run dev
```

## Notes

- The app is now run as one Laravel project.
- The old separate `frontend/` folder is no longer required for normal app usage.

## Documentation

- [SETUP_GUIDE.md](./SETUP_GUIDE.md)
- [laravel-app/README.md](./laravel-app/README.md)

## UI Baseline

The current frontend UI is locked as the default baseline.

- Layout and style should stay the same unless a redesign is explicitly requested.
- Baseline rules are documented in [copilot-instructions.md](./copilot-instructions.md).

## Git Sync (Bring latest UI)

If your latest UI is in a remote Git repository, use these scripts from project root:

- `git-sync-once.bat` : Connects to `origin` (asks for URL if missing), fetches, and pulls latest branch changes.
- `git-sync-auto.bat` : Runs sync every 60 seconds.
- `git-sync-push-once.bat` : Pulls latest, auto-commits your local changes, and pushes.
- `git-sync-auto-push.bat` : Runs pull + auto-commit + push every 60 seconds.

### First time

1. Run `git-sync-once.bat`
2. Paste your repository URL when prompted
3. If pull fails because the branch is new, run the shown push command once

After that, run `git-sync-auto.bat` to keep pulling updates automatically.

### Auto push mode

Use `git-sync-auto-push.bat` if you want continuous backup of your local UI changes to your remote repository.

If commit fails the first time, set your Git identity once:

```bash
git config --global user.name "Your Name"
git config --global user.email "you@example.com"
```

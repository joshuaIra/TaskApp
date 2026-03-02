# TaskApp

TaskApp is a role-based task management website built with Laravel + Vue.
It helps teams create, assign, track, and complete tasks with different access levels for `admin`, `manager`, and `member` users.

## Website Description

TaskApp provides a clean dashboard, task lifecycle management, comments/attachments, and notifications so teams can monitor progress in one place.

## Core Features

- Role-based access control (`admin`, `manager`, `member`)
- Task create/read/update/delete with status and priority tracking
- Admin user management area (admin only)
- Notification APIs (`list`, `read one`, `read all`)
- Vue SPA pages with Laravel-authenticated backend routes

## Project Structure

```
TaskApp/
├── laravel-app/                 # Main application (Laravel + SPA assets)
├── frontend/                    # Legacy/standalone frontend workspace
├── start.bat                    # Windows start helper
├── start.sh                     # macOS/Linux start helper
├── SETUP_GUIDE.md               # Setup documentation
└── README.md
```

## Quick Start (Windows)

```bash
cd laravel-app
composer install
npm install
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Then open: `http://127.0.0.1:8000`

## Development Commands

```bash
# Laravel app
cd laravel-app
php artisan optimize:clear
php artisan route:list

# Frontend assets
npm run dev
npm run build
```

## Test Accounts

All seeded users use this password:

- `Password123!`

Role accounts:

- `admin@test.local`
- `manager@test.local`
- `member@test.local`

## API Access Matrix

- `GET /api/tasks`: all authenticated roles
- `POST /api/tasks`: admin + manager only
- `GET /api/admin/users`: admin only
- `GET /api/notifications`: all authenticated roles

## Additional Docs

- [SETUP_GUIDE.md](./SETUP_GUIDE.md)
- [laravel-app/README.md](./laravel-app/README.md)

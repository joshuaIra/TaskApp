# Setup Guide (Laravel Only)

This project now runs as a single Laravel application from `laravel-app`.

## Architecture

```
TaskApp/
└── laravel-app/   (Backend + Frontend views)
```

## Prerequisites

- PHP 8.2+
- Composer
- Database (SQLite/MySQL/PostgreSQL)
- Node.js 18+ (optional, only for Vite dev workflow)

## Installation

1. Go to Laravel app:
```bash
cd laravel-app
```

2. Install PHP dependencies:
```bash
php ../composer.phar install --ignore-platform-reqs
```

3. Create environment file:
```bash
cp .env.example .env
```

4. Generate app key:
```bash
php artisan key:generate
```

5. Configure database in `.env`, then run:
```bash
php artisan migrate
```

6. Optional seed data:
```bash
php artisan db:seed
```

## Run Application

### Option A: Root helper script (Windows)
Run `start.bat`

### Option B: Root helper script (macOS/Linux)
```bash
bash start.sh
```

### Option C: Manual
```bash
cd laravel-app
php artisan serve
```

Open `http://localhost:8000`.

## Optional: Vite Asset Development

Inside `laravel-app`:

```bash
npm install
npm run dev
```

This is optional and only needed during active frontend asset development.

## Troubleshooting

- Port in use: `php artisan serve --port=8001`
- Migration issues: `php artisan migrate:refresh`
- Missing dependencies: rerun Composer and npm install in `laravel-app`

## Migration Note

The old standalone `frontend/` app is not required for runtime anymore. You can keep it temporarily for reference or remove it after validating the Laravel UI flow.

# TaskApp (Laravel Application)

This folder contains the main TaskApp website and APIs.

## Website Description

TaskApp is a collaborative work tracker where teams manage tasks, monitor progress, and receive notifications, with permissions enforced by role (`admin`, `manager`, `member`).

## Tech Stack

- Laravel 12
- Vue 3 + Pinia + Vue Router
- Vite asset pipeline
- SQLite/MySQL compatible database

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Run

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

In a separate terminal (optional during development):

```bash
npm run dev
```

Production assets:

```bash
npm run build
```

## Auth & Roles

- `admin`: full access including admin users endpoints/pages
- `manager`: task write access, no admin users access
- `member`: read tasks + personal notifications, no task write/admin users

## Key Endpoints

- `GET /api/tasks`
- `POST /api/tasks`
- `GET /api/admin/users`
- `GET /api/notifications`
- `POST /api/notifications/read-all`

## Test Credentials

- `admin@test.local` / `Password123!`
- `manager@test.local` / `Password123!`
- `member@test.local` / `Password123!`

#!/bin/bash

# Task App Start Script - Single Laravel App

echo ""
echo "========================================"
echo "   Task App - Laravel Only"
echo "========================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP is not installed!"
    echo "Please install PHP"
    exit 1
fi

echo "Starting Laravel app..."
echo ""

# Start Laravel in background
echo "[Laravel] Starting Laravel server on port 8000..."
cd laravel-app
php artisan serve &
LARAVEL_PID=$!
echo "[Laravel] Server started (PID: $LARAVEL_PID)"

echo ""

# Wait a moment for Laravel to start
sleep 3

# Optional: Start Vite for Laravel assets if Node.js is available
if command -v node &> /dev/null; then
    echo "[Assets] Starting Laravel Vite dev server..."
    npm run dev &
    VITE_PID=$!
    echo "[Assets] Vite server started (PID: $VITE_PID)"
else
    echo "[Assets] Node.js not found. Skipping Vite dev server."
fi

echo ""
echo "App: http://localhost:8000"
echo "Press Ctrl+C to stop"

# Clean up on exit
trap 'kill "$LARAVEL_PID" >/dev/null 2>&1; if [ -n "${VITE_PID:-}" ]; then kill "$VITE_PID" >/dev/null 2>&1; fi' EXIT INT TERM

wait $LARAVEL_PID

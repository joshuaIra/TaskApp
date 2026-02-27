# UI Baseline Lock (TaskApp)

Use the current frontend UI as the fixed baseline.

## Baseline files

- `laravel-app/resources/js/App.vue`
- `laravel-app/resources/js/components/Header.vue`
- `laravel-app/resources/js/components/Sidebar.vue`
- `laravel-app/resources/js/components/Footer.vue`

## Rules

1. Do not redesign or replace layout/style in baseline files unless the user explicitly asks.
2. Allowed changes by default: bug fixes, responsiveness fixes, accessibility fixes, and small text/content updates.
3. Keep visual structure: header on top, sidebar on the side, main content center, footer at bottom.
4. Preserve existing color theme/look-and-feel unless user requests a visual refresh.

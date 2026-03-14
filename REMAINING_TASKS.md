# Remaining Tasks (Execution Plan)

This file is the **single source of truth** for what is still pending.
We will complete tasks in order, one by one.

## How we use this
- Work from top to bottom.
- Only one item is active at a time.
- Mark `[x]` when fully done and verified.

## Phase 1 — Navigation Completion
- [x] Add `Settings` and `Reports` links in navigation (header/sidebar) with role-aware visibility.
- [x] Add route guards for `Settings` and `Reports` pages.

## Phase 2 — Reports & Export
- [x] Build Reports page with filters (date range, status, priority, assignee, manager, due window).
- [x] Add CSV export from Reports page.
- [x] Add quick CSV export entry from dashboard/navbar.

## Phase 3 — Settings
- [x] Build Settings page: app name + logo upload.
- [x] Add email sender override and SMTP test action.
- [x] Add account/security settings: password change, timezone, HTTPS enforcement toggle.

## Phase 4 — Reminders & Scheduling
- [x] Reminder rules: daily, weekly, every N days.
- [x] Weekdays-only reminder option.
- [x] Per-assignee reminder configuration.
- [ ] Quiet hours (per-user + default).
- [ ] Cron/command job for reminder emails.

## Phase 5 — UX and Data Table Quality
- [ ] Add loading + error states to key pages.
- [ ] Add pagination, sorting, and search to task/user lists.
- [ ] Improve responsive behavior and accessibility checks.
- [ ] Add flash/success/error messages for main actions.

## Phase 6 — Testing & Final Polish
- [ ] Add/expand feature tests for role access matrix.
- [ ] Add tests for reports, settings, and reminders.
- [ ] Run full regression pass (admin/manager/member flows).
- [ ] Performance pass and bug-fix sweep.

---

## Active Task
- [ ] **Task 11:** Quiet hours (per-user + default).

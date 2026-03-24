# Code Optimization Plan

## Summary of Issues Found

### 1. Duplicate Code Between `frontend/` and `laravel-app/resources/js/`

Both directories contain nearly identical files:
- `services/api.js` 
- `stores/taskStore.js`
- `stores/uiStore.js`
- Multiple pages (CreateTask.vue, Dashboard.vue, TaskDetail.vue, TasksList.vue, Contact.vue)
- Multiple components (Footer.vue, Header.vue, Modal.vue, NotificationCenter.vue, Sidebar.vue, SkeletonCard.vue, TaskCard.vue)

### 2. Feature Inconsistencies

| Feature | frontend | laravel-app |
|---------|----------|-------------|
| API endpoints | Basic (getAll, create, update, delete) | Advanced (import, progress, member-update, admin settings) |
| Task status normalization | ✅ Has `normalizeStatus`, `tasksByStatus`, `moveTask` | ❌ Missing |
| User-scoped caching | ❌ Missing | ✅ Has `ensureUserScopedCache` |
| Notification duration | Hardcoded 3000ms | Configurable |
| Dark mode class | `dark` | `app-dark` |
| SSR checks | ❌ Missing | ✅ Has `typeof window/document` checks |

---

## Optimization Strategy

### Option A: Merge into Single Shared Module (Recommended)
Create a `shared/` folder at project root for common code:
```
TaskApp/
├── shared/
│   └── js/
│       ├── services/api.js      # Unified, feature-complete API service
│       ├── stores/
│       │   ├── taskStore.js     # Merged with all features
│       │   └── uiStore.js       # Unified with SSR support
│       └── composables/         # Shared composables
├── frontend/                    # Vite SPA (imports from shared/)
└── laravel-app/                 # Laravel + Inertia (imports from shared/)
```

### Option B: Inline Optimization (Minimal Changes)
Keep structure but consolidate within each duplicate file:
1. Merge best features into both copies
2. Ensure consistency in API service
3. Add missing functions to each store

---

## Detailed Implementation Plan (Option B - Inline)

### Phase 1: Unified API Service
**Files to update:**
- `frontend/resources/js/services/api.js`
- `laravel-app/resources/js/services/api.js`

**Changes:**
- Merge all endpoints from laravel-app into frontend
- Add consistent error handling
- Add CSRF token support
- Add response interceptors

### Phase 2: Unified Task Store
**Files to update:**
- `frontend/resources/js/stores/taskStore.js`
- `laravel-app/resources/js/stores/taskStore.js`

**Changes:**
- Add `ensureUserScopedCache` from laravel-app to frontend
- Add `fetchTasks`, `tasksByStatus`, `moveTask` from frontend to laravel-app
- Consolidate `normalizeStatus` logic

### Phase 3: Unified UI Store  
**Files to update:**
- `frontend/resources/js/stores/uiStore.js`
- `laravel-app/resources/js/stores/uiStore.js`

**Changes:**
- Use consistent dark mode class (recommend `app-dark`)
- Add configurable notification duration
- Add SSR safety checks

### Phase 4: Component Optimization
**Duplicate components to review:**
- Header.vue, Footer.vue, Sidebar.vue
- Modal.vue, NotificationCenter.vue
- TaskCard.vue, SkeletonCard.vue

**Actions:**
- Identify any differences
- Keep the more feature-complete version
- Optionally add to shared module

---

## Follow-up Steps

1. **Confirm optimization approach** (Option A or B)
2. **Backup current files** (git commit recommended)
3. **Execute Phase 1-4** sequentially
4. **Test both applications** to ensure functionality
5. **Remove any dead code** after verification

---

## Risk Assessment

- **Low Risk**: Phase 2-3 (stores) - internal logic only
- **Medium Risk**: Phase 1 (API) - affects all HTTP calls
- **Medium Risk**: Phase 4 (Components) - may affect UI

**Mitigation**: Test each phase thoroughly before proceeding.


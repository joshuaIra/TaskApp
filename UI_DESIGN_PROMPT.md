# Task Management System - UI/UX Design Prompt

## Project Overview
Build a modern, professional Task Management Web Application with a sleek, intuitive interface. The system should provide a seamless experience for managing tasks, teams, and projects with role-based access control.

---

## Design Philosophy
- **Style**: Modern minimalist with subtle depth through shadows and gradients
- **Theme**: Dark mode as default with elegant light mode toggle
- **Typography**: Clean sans-serif fonts (Inter or similar)
- **Color Palette**:
  - Primary: #2563EB (Blue 600)
  - Secondary: #7C3AED (Violet 600)
  - Success: #10B981 (Emerald 500)
  - Warning: #F59E0B (Amber 500)
  - Danger: #EF4444 (Red 500)
  - Background Dark: #0F172A (Slate 900)
  - Background Light: #F8FAFC (Slate 50)
  - Surface Dark: #1E293B (Slate 800)
  - Surface Light: #FFFFFF

---

## Page-by-Page UI Requirements

### 1. Login Page (`/login`)
**Layout**:
- Centered card layout with backdrop blur
- Animated background with subtle gradient or geometric patterns
- Company logo at top

**Components**:
- Email input with icon
- Password input with show/hide toggle
- "Remember me" checkbox
- "Forgot password?" link
- Login button (full width, prominent)
- Subtle social login options if needed

**Interactions**:
- Input focus states with glow effect
- Loading spinner on submit
- Error shake animation on invalid credentials

---

### 2. Dashboard (`/`)
**Layout**:
- Full-width responsive grid
- Top stats row (4 KPI cards)
- Main chart area (2/3 width)
- Side panel with priority distribution (1/3 width)
- Recent tasks table at bottom

**Components**:

**KPI Cards** (4 cards in a row):
- Total Tasks (with trend indicator)
- In Progress (with progress bar)
- Completed (with completion rate)
- Overdue (if any)
- Each card: Icon, value, label, trend percentage
- Hover: Subtle lift with shadow increase

**Task Status Chart**:
- Doughnut or pie chart showing: Pending, In Progress, Completed
- Interactive legend
- Center text showing total

**Priority Distribution**:
- Horizontal bar chart or list
- High (red), Medium (yellow), Low (green) indicators
- Count badges

**Recent Tasks Table**:
- 5 most recent tasks
- Columns: Priority dot, Title, Status badge, Due date, Assignee avatar
- Click row to navigate to task detail

**Quick Actions**:
- "New Task" floating action button or prominent button

---

### 3. Tasks List Page (`/tasks`)
**Layout**:
- Header with title and "Create Task" button
- Filter bar (horizontal scrollable on mobile)
- View toggle (grid/list)
- Tasks container (responsive grid or table)
- Pagination at bottom

**Filter Bar Components**:
- Search input
- Status dropdown (All, Pending, In Progress, Completed)
- Priority dropdown (All, High, Medium, Low)
- Assignee dropdown (All, My Tasks)
- Date range picker
- Clear filters button

**View Options**:
- Grid view: Task cards in columns
- List view: Table format

**Task Card Components**:
- Priority color indicator (left border)
- Title (bold)
- Description preview (2 lines max)
- Due date with icon
- Assignee avatars (stacked, max 3 shown)
- Status badge
- Priority badge
- Actions menu (three dots)
- Hover: Elevated shadow, slight scale

**Pagination**:
- Page numbers
- Items per page selector
- Total count display

---

### 4. My Tasks Page (`/tasks?filter=my`)
- Same as Tasks List but pre-filtered to assigned tasks
- Prominent "Assigned to me" badge
- Quick complete checkbox for each task

---

### 5. Create Task Page (`/tasks/create`)
**Layout**:
- Two-column form on desktop
- Single column on mobile

**Form Fields**:
- Title input (required, max 255 chars)
- Description textarea (rich text editor optional)
- Priority select (High, Medium, Low) with color indicators
- Due date picker (calendar widget)
- Assignees multi-select (searchable dropdown with avatars)
- Reminder rules section:
  - Enable/disable toggle
  - Frequency: Daily, Weekly, Every N days
  - Weekdays only checkbox
  - Time picker
- Attachments drag-and-drop zone

**Actions**:
- Save as Draft button (secondary)
- Create Task button (primary)
- Cancel button (text)

---

### 6. Task Detail Page (`/tasks/{id}`)
**Layout**:
- Header: Title, status, priority, due date, actions menu
- Three-column layout on desktop:
  - Main: Description, updates, comments
  - Right: Details (assignees, reminders, attachments)
  - Left (optional): Navigation

**Header Components**:
- Breadcrumb
- Title (editable inline)
- Status dropdown
- Priority dropdown
- Due date with calendar
- Edit button
- Delete button
- Share button

**Main Content**:
- Description section (editable)
- Progress tracker (Not Started → In Progress → Completed)
- Activity timeline:
  - Status changes
  - Comments
  - File uploads
  - Assignment changes

**Comments Section**:
- Comment input with rich text
- Attach button
- Existing comments list with:
  - User avatar
  - Name and timestamp
  - Content
  - Edit/Delete actions

**Right Sidebar - Details**:
- **Assignees Card**:
  - List of assigned users with avatars
  - Add/Remove buttons
  - Individual reminder settings per assignee

- **Reminders Card**:
  - List of reminder rules
  - Enable/disable per rule
  - Edit/Delete actions
  - "Add Reminder" button

- **Attachments Card**:
  - Grid of file thumbnails/icons
  - File name, size, upload date
  - Download/View/Delete actions
  - Upload dropzone

- **Metadata Card**:
  - Created by
  - Created date
  - Last updated

---

### 7. Admin Users Page (`/admin/users`)
**Layout**:
- Header with title and "Add User" button
- Filter/search bar
- Users table

**Table Columns**:
- Checkbox (bulk select)
- Avatar + Name
- Email
- Role (Admin/Manager/Member) with badge
- Status (Active/Inactive) with toggle
- Created date
- Actions (Edit, Delete)

**Features**:
- Search by name or email
- Filter by role
- Filter by status
- Sort by any column
- Bulk actions (activate, deactivate, delete)

**Add/Edit User Modal**:
- Name input
- Email input
- Role select
- Temporary password input (for new users)
- Generate password button
- Send welcome email checkbox

---

### 8. Settings Page (`/settings`)
**Layout**:
- Tabbed interface or vertical sidebar
- Settings organized by category

**Tabs/Sections**:

**General Settings**:
- App name input
- Logo upload (with preview)
- Timezone select

**Email Settings**:
- SMTP host input
- SMTP port input
- SMTP encryption dropdown (None, SSL, TLS)
- SMTP username input
- SMTP password input (masked)
- From name input
- From email input
- "Send Test Email" button

**Reminder Settings**:
- Default quiet hours:
  - Start time picker
  - End time picker
- Enable/disable default quiet hours

**Security Settings** (optional):
- Password requirements display
- Session timeout settings

---

### 9. Profile Page (`/profile`)
**Layout**:
- Centered card layout

**Sections**:
- **Profile Info**:
  - Avatar upload (with preview)
  - Name input
  - Email input (read-only or editable based on settings)

- **Password Change**:
  - Current password input
  - New password input
  - Confirm password input
  - Password strength indicator
  - Update Password button

- **Preferences**:
  - Timezone select
  - Notification preferences:
    - Email notifications toggle
    - In-app notifications toggle
  - Theme preference (System, Light, Dark)

---

### 10. Reports Page (`/reports`)
**Layout**:
- Header with title and "Export CSV" button
- Filter bar
- Preview table
- Export options

**Filter Components**:
- Date range picker (From - To)
- Status multi-select
- Priority multi-select
- Assignee multi-select
- Manager multi-select (for admins/managers)
- Due date window (This week, This month, Custom)

**Preview Table**:
- Show first 10-20 filtered results
- Columns: Task ID, Title, Status, Priority, Assignee, Manager, Due Date, Created

**Export**:
- "Export CSV" button
- Download starts automatically or shows preview

---

### 11. Notifications Dropdown
**Components**:
- Bell icon with unread count badge
- Dropdown panel:
  - Header: "Notifications" + "Mark all as read" button
  - List of notifications (max 10)
  - Each notification:
    - Icon (based on type)
    - Message
    - Time ago
    - Unread indicator (dot)
  - "View all" link

**Notification Types**:
- Task assigned
- Task completed
- Comment added
- Reminder
- Task overdue

---

## Common Components

### Sidebar Navigation
**Items**:
- Dashboard
- All Tasks
- My Tasks
- Reports (for managers/admins)
- Admin → Users (admin only)
- Settings (admin only)
- Profile

**States**:
- Default: Icon + label
- Hover: Background highlight
- Active: Blue accent bar on left, highlighted background

### Header
- Logo/App name (left)
- Global search (center, desktop)
- Theme toggle
- Notifications bell
- User avatar dropdown (profile, settings, logout)

### Modals
- Centered with backdrop blur
- Close button (X) top right
- Title
- Content area
- Footer with actions (Cancel, Confirm)

### Forms
- Floating labels or top labels
- Validation states (error, success)
- Helper text
- Required indicator (*)

### Buttons
- Primary: Filled blue
- Secondary: Outlined
- Danger: Red
- Ghost: No background
- Sizes: sm, md, lg

### Form Inputs
- Rounded corners
- Focus ring
- Error state with red border and message
- Success state with green check

### Cards
- Rounded corners (lg or xl)
- Subtle shadow
- Hover state for interactive cards

### Tables
- Striped rows (optional)
- Hover highlight
- Sortable headers with icons
- Responsive: horizontal scroll on mobile

### Dropdowns
- Searchable for long lists
- Multi-select support
- Clear selection option

### Date Pickers
- Calendar view
- Quick selections (Today, Tomorrow, This Week)
- Time selection for due dates

---

## Responsive Breakpoints
- Mobile: < 640px
- Tablet: 640px - 1024px
- Desktop: > 1024px

**Mobile Adaptations**:
- Hamburger menu for sidebar
- Stacked layouts
- Horizontal scroll for filter bars
- Bottom navigation or slide-out menu
- Larger touch targets

---

## Animations & Transitions
- Page transitions: Fade + slide
- Card hover: Scale 1.02 + shadow
- Button hover: Background color shift
- Modal: Fade in + scale up
- Dropdown: Fade + slide down
- Loading: Skeleton screens
- Success: Checkmark animation
- Notifications: Slide in from right

---

## Accessibility Requirements
- ARIA labels
- Keyboard navigation
- Focus indicators
- Color contrast (WCAG AA)
- Screen reader friendly
- Reduced motion support

---

## Performance Considerations
- Lazy loading for images
- Virtual scrolling for long lists
- Optimistic UI updates
- Cached API responses
- Code splitting by route

---

This prompt should be used with an AI code generation tool (like v0, bolt.new, or similar) to build the complete UI system. The resulting application should be fully functional, visually stunning, and provide an excellent user experience across all devices.

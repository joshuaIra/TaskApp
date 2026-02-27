# README for Frontend

## Frontend - Vue.js with Vite

This is the frontend application for the Task App, built with Vue.js 3 and Vite.

### Installation

```bash
npm install
```

### Development

```bash
npm run dev
```

Runs at `http://localhost:3000`

### Build

```bash
npm run build
```

### Preview

```bash
npm run preview
```

### Environment Variables

Create `.env.local` file:
```
VITE_API_URL=http://localhost:8000/api
```

### File Structure

```
frontend/
├── index.html              # Entry HTML file
├── package.json            # Dependencies
├── vite.config.js          # Vite config
├── resources/
│   ├── js/
│   │   ├── app.js         # Main entry point
│   │   └── App.vue        # Root component
│   └── css/
│       └── app.css        # Tailwind styles
└── public/                # Static assets
```

### API Communication

The frontend communicates with the backend API:

```javascript
// Using axios (configured globally as this.$http)
this.$http.get('/tasks')
this.$http.post('/tasks', taskData)
this.$http.put(`/tasks/${id}`, taskData)
this.$http.delete(`/tasks/${id}`)
```

### Styling

Uses Tailwind CSS for styling. Classes are available globally.

### Components

All Vue components should be in `.vue` format in `resources/js/`

Example component:
```vue
<template>
  <div class="flex justify-center">
    <h1 class="text-3xl font-bold">Hello</h1>
  </div>
</template>

<script>
export default {
  name: 'MyComponent',
}
</script>
```

import axios from 'axios';

const resolveApiUrl = () => {
  if (import.meta.env.VITE_API_URL) {
    return import.meta.env.VITE_API_URL;
  }

  if (typeof window !== 'undefined') {
    const devPorts = new Set(['5173', '5174', '5175']);
    if (devPorts.has(window.location.port)) {
      return 'http://127.0.0.1:8000/api';
    }
  }

  return '/api';
};

const API_URL = resolveApiUrl();

const api = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

const multipartConfig = {
  headers: { 'Content-Type': 'multipart/form-data' },
};

const postMultipart = (url, formData) => api.post(url, formData, multipartConfig);

const csrfToken = typeof document !== 'undefined'
  ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  : null;

if (csrfToken) {
  api.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

api.interceptors.response.use(
  (response) => {
    const contentType = String(response?.headers?.['content-type'] || '').toLowerCase();
    if (contentType.includes('text/html')) {
      return Promise.reject(new Error('Unexpected HTML response. Your session may have expired.'));
    }

    return response;
  },
  (error) => Promise.reject(error)
);

// Task endpoints
export const taskService = {
  getAll: () => api.get('/tasks'),
  getById: (id) => api.get(`/tasks/${id}`),
  getAssignees: (params = {}) => api.get('/assignees', { params }),
  create: (data) => api.post('/tasks', data),
  import: (formData) => postMultipart('/tasks/import', formData),
  updateProgress: (id, data) => api.patch(`/tasks/${id}/progress`, data),
  update: (id, data) => api.put(`/tasks/${id}`, data),
  delete: (id) => api.delete(`/tasks/${id}`),
  getMyTasks: () => api.get('/my-tasks'),
};

// Comment endpoints
export const commentService = {
  getByTask: (taskId) => api.get(`/tasks/${taskId}/comments`),
  create: (taskId, data) => api.post(`/tasks/${taskId}/comments`, data),
  update: (id, data) => api.put(`/comments/${id}`, data),
  delete: (id) => api.delete(`/comments/${id}`),
};

// Attachment endpoints
export const attachmentService = {
  upload: (taskId, formData) => postMultipart(`/tasks/${taskId}/attachments`, formData),
  download: (id) => api.get(`/attachments/${id}/download`, { responseType: 'blob' }),
  delete: (id) => api.delete(`/attachments/${id}`),
};

// Notification endpoints
export const notificationService = {
  getAll: () => api.get('/notifications'),
  markAsRead: (id) => api.post(`/notifications/${id}/read`),
  markAllAsRead: () => api.post('/notifications/read-all'),
};

// User endpoints
export const userService = {
  getProfile: () => api.get('/profile'),
  updateProfile: (data) => api.put('/profile', data),
};

// Settings endpoints
export const settingsService = {
  getBranding: () => api.get('/admin/settings/branding'),
  updateBranding: (formData) => postMultipart('/admin/settings/branding', formData),
  getEmailSettings: () => api.get('/admin/settings/email'),
  updateEmailSettings: (data) => api.post('/admin/settings/email', data),
  testEmailSettings: (data) => api.post('/admin/settings/email/test', data),
  getSecuritySettings: () => api.get('/admin/settings/security'),
  updateSecuritySettings: (data) => api.post('/admin/settings/security', data),
  updatePassword: (data) => api.post('/admin/settings/security/password', data),
  getReminderSettings: () => api.get('/admin/settings/reminders'),
  updateReminderSettings: (data) => api.post('/admin/settings/reminders', data),
  getAssigneeReminderSettings: () => api.get('/admin/settings/reminders/assignees'),
  updateAssigneeReminderSettings: (data) => api.post('/admin/settings/reminders/assignees', data),
};

// Admin user management endpoints
export const adminUserService = {
  getAll: () => api.get('/admin/users'),
  create: (data) => api.post('/admin/users', data),
  update: (id, data) => api.put(`/admin/users/${id}`, data),
  toggleActive: (id) => api.post(`/admin/users/${id}/toggle`),
  delete: (id) => api.delete(`/admin/users/${id}`),
};

export default api;

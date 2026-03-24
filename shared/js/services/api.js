import axios from 'axios';

// ====================
// URL Resolution
// ====================
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

// ====================
// Axios Instance
// ====================
const api = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// ====================
// Multipart Config
// ====================
const multipartConfig = {
  headers: { 'Content-Type': 'multipart/form-data' },
};

const postMultipart = (url, formData) => api.post(url, formData, multipartConfig);

// ====================
// CSRF Token
// ====================
const csrfToken = typeof document !== 'undefined'
  ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  : null;

if (csrfToken) {
  api.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

// ====================
// Response Interceptors
// ====================
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

// ====================
// Task Service
// ====================
export const taskService = {
  getAll: (params = {}) => api.get('/tasks', { params }),
  getById: (id) => api.get(`/tasks/${id}`),
  getAssignees: (params = {}) => api.get('/assignees', { params }),
  create: (data) => api.post('/tasks', data),
  import: (formData) => postMultipart('/tasks/import', formData),
  updateProgress: (id, data) => api.patch(`/tasks/${id}/progress`, data),
  memberUpdate: (id, data) => api.patch(`/tasks/${id}/member-update`, data),
  update: (id, data) => api.put(`/tasks/${id}`, data),
  delete: (id) => api.delete(`/tasks/${id}`),
  getMyTasks: () => api.get('/my-tasks'),
  move: (id, status) => api.patch(`/tasks/${id}/move`, { status }),
};

// ====================
// Comment Service
// ====================
export const commentService = {
  getByTask: (taskId) => api.get(`/tasks/${taskId}/comments`),
  create: (taskId, data) => api.post(`/tasks/${taskId}/comments`, data),
  update: (id, data) => api.put(`/comments/${id}`, data),
  delete: (id) => api.delete(`/comments/${id}`),
};

// ====================
// Attachment Service
// ====================
export const attachmentService = {
  upload: (taskId, formData) => postMultipart(`/tasks/${taskId}/attachments`, formData),
  download: (id) => api.get(`/attachments/${id}/download`, { responseType: 'blob' }),
  delete: (id) => api.delete(`/attachments/${id}`),
};

// ====================
// Notification Service
// ====================
export const notificationService = {
  getAll: () => api.get('/notifications'),
  markAsRead: (id) => api.post(`/notifications/${id}/read`),
  markAllAsRead: () => api.post('/notifications/read-all'),
};

// ====================
// User Service
// ====================
export const userService = {
  getProfile: () => api.get('/profile'),
  updateProfile: (data) => api.put('/profile', data),
};

// ====================
// Settings Service (Admin)
// ====================
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

// ====================
// Admin User Service
// ====================
export const adminUserService = {
  getAll: () => api.get('/admin/users'),
  create: (data) => api.post('/admin/users', data),
  update: (id, data) => api.put(`/admin/users/${id}`, data),
  toggleActive: (id) => api.post(`/admin/users/${id}/toggle`),
  delete: (id) => api.delete(`/admin/users/${id}`),
};

// ====================
// Export Default
// ====================
export default api;


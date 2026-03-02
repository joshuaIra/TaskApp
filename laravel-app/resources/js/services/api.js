import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Task endpoints
export const taskService = {
  getAll: () => api.get('/tasks'),
  getById: (id) => api.get(`/tasks/${id}`),
  create: (data) => api.post('/tasks', data),
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
  upload: (taskId, formData) => api.post(`/tasks/${taskId}/attachments`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  }),
  download: (id) => api.get(`/attachments/${id}/download`),
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
  updateBranding: (formData) => api.post('/admin/settings/branding', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  }),
  getEmailSettings: () => api.get('/admin/settings/email'),
  updateEmailSettings: (data) => api.post('/admin/settings/email', data),
  testEmailSettings: (data) => api.post('/admin/settings/email/test', data),
  getSecuritySettings: () => api.get('/admin/settings/security'),
  updateSecuritySettings: (data) => api.post('/admin/settings/security', data),
  updatePassword: (data) => api.post('/admin/settings/security/password', data),
  getReminderSettings: () => api.get('/admin/settings/reminders'),
  updateReminderSettings: (data) => api.post('/admin/settings/reminders', data),
};

export default api;

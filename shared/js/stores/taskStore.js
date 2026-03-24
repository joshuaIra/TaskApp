import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { taskService } from '../services/api';

// ====================
// Status Normalization
// ====================
const normalizeStatus = (status) => {
  if (!status) return 'pending';
  if (status === 'todo') return 'pending';
  if (status === 'done') return 'completed';
  return status; // pending, in_progress, review, completed
};

// ====================
// User ID Helper
// ====================
const getCurrentUserId = () => {
  if (typeof window !== 'undefined') {
    return Number(window.TaskAppAuth?.user?.id || 0);
  }
  return 0;
};

export const useTaskStore = defineStore('tasks', () => {
  // ====================
  // State
  // ====================
  const tasks = ref([]);
  const myTasks = ref([]);
  const selectedTask = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // Cache flags
  const hasLoadedAllTasks = ref(false);
  const hasLoadedMyTasks = ref(false);
  const loadedForUserId = ref(null);

  // ====================
  // Computed
  // ====================
  const taskCount = computed(() => tasks.value.length);
  const myTaskCount = computed(() => myTasks.value.length);

  // ====================
  // Private Methods
  // ====================
  const ensureUserScopedCache = () => {
    const currentUserId = getCurrentUserId();

    if (loadedForUserId.value !== currentUserId) {
      tasks.value = [];
      myTasks.value = [];
      selectedTask.value = null;
      hasLoadedAllTasks.value = false;
      hasLoadedMyTasks.value = false;
      loadedForUserId.value = currentUserId;
    }
  };

  const tasksByStatus = (status) => {
    return tasks.value.filter(t => normalizeStatus(t.status) === status);
  };

  // ====================
  // Actions
  // ====================
  const fetchTasks = async () => {
    try {
      const res = await taskService.getAll();
      const list = Array.isArray(res.data) ? res.data : (res.data?.data ?? []);
      tasks.value = list.map(t => ({ ...t, status: normalizeStatus(t.status) }));
    } catch (e) {
      tasks.value = [];
      error.value = e?.response?.data?.message || 'Failed to load tasks';
    }
  };

  const fetchAllTasks = async (forceRefresh = false) => {
    ensureUserScopedCache();

    if (!forceRefresh && hasLoadedAllTasks.value) return;

    loading.value = true;
    error.value = null;

    try {
      const response = await taskService.getAll();
      const list = Array.isArray(response.data) ? response.data : (response.data?.data ?? []);
      tasks.value = list.map(t => ({ ...t, status: normalizeStatus(t.status) }));
      hasLoadedAllTasks.value = true;
    } catch (err) {
      tasks.value = [];
      error.value = err?.response?.data?.message || 'Failed to load tasks';
      hasLoadedAllTasks.value = false;
    } finally {
      loading.value = false;
    }
  };

  const fetchMyTasks = async (forceRefresh = false) => {
    ensureUserScopedCache();

    if (!forceRefresh && hasLoadedMyTasks.value) return;

    loading.value = true;
    error.value = null;

    try {
      const response = await taskService.getMyTasks();
      myTasks.value = Array.isArray(response.data) ? response.data : [];
      hasLoadedMyTasks.value = true;
    } catch (err) {
      myTasks.value = [];
      error.value = err?.response?.data?.message || 'Failed to load my tasks';
      hasLoadedMyTasks.value = false;
    } finally {
      loading.value = false;
    }
  };

  const fetchTaskById = async (id) => {
    ensureUserScopedCache();

    loading.value = true;
    error.value = null;

    try {
      const response = await taskService.getById(id);
      selectedTask.value = response.data;
    } catch (err) {
      selectedTask.value = tasks.value.find(t => t.id == id) || null;
      error.value = err?.response?.data?.message || 'Task not found';
    } finally {
      loading.value = false;
    }
  };

  const createTask = async (taskData) => {
    ensureUserScopedCache();

    loading.value = true;
    error.value = null;

    try {
      const response = await taskService.create(taskData);
      const createdTask = response?.data;

      if (!createdTask || typeof createdTask !== 'object' || !createdTask.id) {
        throw new Error('Invalid create task response');
      }

      tasks.value.push(createdTask);
      hasLoadedAllTasks.value = true;
      return createdTask;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateTask = async (id, taskData) => {
    ensureUserScopedCache();

    loading.value = true;
    error.value = null;

    try {
      const response = await taskService.update(id, taskData);
      const updatedTask = response.data;

      const index = tasks.value.findIndex(t => t.id === id);
      if (index !== -1) {
        tasks.value[index] = { ...updatedTask, status: normalizeStatus(updatedTask.status) };
      }
      if (selectedTask.value?.id === id) {
        selectedTask.value = { ...updatedTask, status: normalizeStatus(updatedTask.status) };
      }
      return updatedTask;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteTask = async (id) => {
    ensureUserScopedCache();

    loading.value = true;
    error.value = null;

    try {
      await taskService.delete(id);
      tasks.value = tasks.value.filter(t => t.id !== id);
      myTasks.value = myTasks.value.filter(t => t.id !== id);
      if (selectedTask.value?.id === id) {
        selectedTask.value = null;
      }
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const moveTask = async (taskId, newStatus) => {
    const idx = tasks.value.findIndex(t => t.id === taskId);
    if (idx === -1) return;

    const prev = tasks.value[idx].status;
    tasks.value[idx].status = newStatus;

    try {
      await taskService.move(taskId, newStatus);
    } catch (e) {
      tasks.value[idx].status = prev;
      console.error('moveTask failed; reverted', e);
      throw e;
    }
  };

  // ====================
  // Return Public API
  // ====================
  return {
    // State
    tasks,
    myTasks,
    selectedTask,
    loading,
    error,
    // Computed
    taskCount,
    myTaskCount,
    // Actions
    fetchAllTasks,
    fetchMyTasks,
    fetchTaskById,
    createTask,
    updateTask,
    deleteTask,
    fetchTasks,
    tasksByStatus,
    moveTask,
  };
});


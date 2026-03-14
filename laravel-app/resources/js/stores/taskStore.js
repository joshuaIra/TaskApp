import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { taskService } from '../services/api';

export const useTaskStore = defineStore('tasks', () => {
  const tasks = ref([]);
  const myTasks = ref([]);
  const selectedTask = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const hasLoadedAllTasks = ref(false);
  const hasLoadedMyTasks = ref(false);
  const loadedForUserId = ref(null);

  const getCurrentUserId = () => Number(window.TaskAppAuth?.user?.id || 0);

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

  const fetchAllTasks = async (forceRefresh = false) => {
    ensureUserScopedCache();

    if (!forceRefresh && hasLoadedAllTasks.value) return;

    loading.value = true;
    error.value = null;
    try {
      const response = await taskService.getAll();
      tasks.value = response.data;
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
      myTasks.value = response.data;
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
      const index = tasks.value.findIndex(t => t.id === id);
      if (index !== -1) {
        tasks.value[index] = response.data;
      }
      if (selectedTask.value?.id === id) {
        selectedTask.value = response.data;
      }
      return response.data;
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

  const taskCount = computed(() => tasks.value.length);
  const myTaskCount = computed(() => myTasks.value.length);

  return {
    tasks,
    myTasks,
    selectedTask,
    loading,
    error,
    taskCount,
    myTaskCount,
    fetchAllTasks,
    fetchMyTasks,
    fetchTaskById,
    createTask,
    updateTask,
    deleteTask,
  };
});

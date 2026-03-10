import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { taskService } from '../services/api';

export const useTaskStore = defineStore('tasks', () => {
  const tasks = ref([]);
  const myTasks = ref([]);
  const selectedTask = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const normalizeStatus = (s) => {
  if (!s) return "todo";
  if (s === "pending") return "todo";
  if (s === "completed") return "done";
  return s; // todo, in_progress, review, done
  };

  const tasksByStatus = (status) =>
  tasks.value.filter(t => normalizeStatus(t.status) === status);

  const fetchTasks = async () => {
  try {
    const res = await taskService.getAll();
    // expecting array
    const list = Array.isArray(res.data) ? res.data : (res.data?.data ?? []);
    tasks.value = list.map(t => ({ ...t, status: normalizeStatus(t.status) }));
  } catch (e) {
    tasks.value = [];
    error.value = e?.response?.data?.message || 'Failed to load tasks';
  }
  };

  const moveTask = async (taskId, newStatus) => {
  const idx = tasks.value.findIndex(t => t.id === taskId);
  if (idx === -1) return;

  const prev = tasks.value[idx].status;
  tasks.value[idx].status = newStatus;

  try {
    // If backend exists, sync it
    await taskService.move(taskId, newStatus);
  } catch (e) {
    // revert
    tasks.value[idx].status = prev;
    console.error("moveTask failed; reverted", e);
    throw e;
  }
  };

  const fetchAllTasks = async () => {
    loading.value = true;
    error.value = null;
    try {
      const response = await taskService.getAll();
      tasks.value = response.data;
    } catch (err) {
      tasks.value = [];
      error.value = err?.response?.data?.message || 'Failed to load tasks';
    } finally {
      loading.value = false;
    }
  };

  const fetchMyTasks = async () => {
    loading.value = true;
    error.value = null;
    try {
      const response = await taskService.getMyTasks();
      myTasks.value = response.data;
    } catch (err) {
      myTasks.value = [];
      error.value = err?.response?.data?.message || 'Failed to load my tasks';
    } finally {
      loading.value = false;
    }
  };

  const fetchTaskById = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await taskService.getById(id);
      selectedTask.value = response.data;
    } catch (err) {
      // Try to find in dummy data
      selectedTask.value = tasks.value.find(t => t.id == id) || null;
      if (!selectedTask.value) {
        error.value = 'Task not found';
      }
    } finally {
      loading.value = false;
    }
  };

  const createTask = async (taskData) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await taskService.create(taskData);
      tasks.value.push(response.data);
      return response.data;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateTask = async (id, taskData) => {
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
    loading.value = true;
    error.value = null;
    try {
      await taskService.delete(id);
      tasks.value = tasks.value.filter(t => t.id !== id);
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
    fetchTasks,
    tasksByStatus,
    moveTask,
  };
});

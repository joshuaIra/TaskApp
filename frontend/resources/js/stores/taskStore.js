import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { taskService } from '../services/api';

export const useTaskStore = defineStore('tasks', () => {
  const tasks = ref([
    {
      id: 1,
      title: 'Design homepage mockup',
      description: 'Create a responsive homepage mockup with Figma',
      priority: 'high',
      status: 'in_progress',
      assigned_to: 'john',
      due_date: '2026-03-05',
      tags: ['design', 'frontend'],
      comments_count: 3,
      attachments_count: 2,
    },
    {
      id: 2,
      title: 'Setup database migrations',
      description: 'Create all necessary database migrations for the project',
      priority: 'high',
      status: 'pending',
      assigned_to: 'jane',
      due_date: '2026-02-28',
      tags: ['backend', 'database'],
      comments_count: 1,
      attachments_count: 0,
    },
    {
      id: 3,
      title: 'Write API documentation',
      description: 'Document all API endpoints with examples',
      priority: 'medium',
      status: 'pending',
      assigned_to: 'bob',
      due_date: '2026-03-10',
      tags: ['documentation', 'backend'],
      comments_count: 0,
      attachments_count: 1,
    },
    {
      id: 4,
      title: 'Fix responsive layout bugs',
      description: 'Fix layout issues on mobile devices',
      priority: 'medium',
      status: 'in_progress',
      assigned_to: 'john',
      due_date: '2026-03-01',
      tags: ['frontend', 'bug'],
      comments_count: 5,
      attachments_count: 3,
    },
    {
      id: 5,
      title: 'Optimize database queries',
      description: 'Profile and optimize slow database queries',
      priority: 'low',
      status: 'pending',
      assigned_to: 'jane',
      due_date: '2026-03-15',
      tags: ['backend', 'performance'],
      comments_count: 2,
      attachments_count: 0,
    },
    {
      id: 6,
      title: 'Setup user authentication',
      description: 'Implement JWT authentication for API',
      priority: 'high',
      status: 'completed',
      assigned_to: 'bob',
      due_date: '2026-02-20',
      tags: ['backend', 'security'],
      comments_count: 4,
      attachments_count: 2,
    },
    {
      id: 7,
      title: 'Create user dashboard',
      description: 'Build main dashboard page with widgets',
      priority: 'medium',
      status: 'in_progress',
      assigned_to: 'john',
      due_date: '2026-03-08',
      tags: ['frontend', 'ui'],
      comments_count: 3,
      attachments_count: 1,
    },
    {
      id: 8,
      title: 'Setup CI/CD pipeline',
      description: 'Configure GitHub Actions for automated testing',
      priority: 'medium',
      status: 'pending',
      assigned_to: 'jane',
      due_date: '2026-03-12',
      tags: ['devops', 'automation'],
      comments_count: 0,
      attachments_count: 0,
    },
  ]);
  const myTasks = ref([
    {
      id: 1,
      title: 'Design homepage mockup',
      description: 'Create a responsive homepage mockup with Figma',
      priority: 'high',
      status: 'in_progress',
      assigned_to: 'john',
      due_date: '2026-03-05',
      tags: ['design', 'frontend'],
      comments_count: 3,
      attachments_count: 2,
    },
    {
      id: 4,
      title: 'Fix responsive layout bugs',
      description: 'Fix layout issues on mobile devices',
      priority: 'medium',
      status: 'in_progress',
      assigned_to: 'john',
      due_date: '2026-03-01',
      tags: ['frontend', 'bug'],
      comments_count: 5,
      attachments_count: 3,
    },
    {
      id: 7,
      title: 'Create user dashboard',
      description: 'Build main dashboard page with widgets',
      priority: 'medium',
      status: 'in_progress',
      assigned_to: 'john',
      due_date: '2026-03-08',
      tags: ['frontend', 'ui'],
      comments_count: 3,
      attachments_count: 1,
    },
  ]);
  const selectedTask = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const fetchAllTasks = async () => {
    // Skip if already have data
    if (tasks.value.length > 0) return;
    
    loading.value = true;
    error.value = null;
    try {
      const response = await taskService.getAll();
      tasks.value = response.data;
    } catch (err) {
      // Silently fail - keep dummy data
      console.log('Using dummy data (API unavailable)');
    } finally {
      loading.value = false;
    }
  };

  const fetchMyTasks = async () => {
    // Skip if already have data
    if (myTasks.value.length > 0) return;
    
    loading.value = true;
    error.value = null;
    try {
      const response = await taskService.getMyTasks();
      myTasks.value = response.data;
    } catch (err) {
      // Silently fail - keep dummy data
      console.log('Using dummy data (API unavailable)');
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
  };
});

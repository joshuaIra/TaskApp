import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { taskService } from '../services/api';

const normalizeStatus = (status) => {
	if (!status) return 'pending';
	if (status === 'todo') return 'pending';
	if (status === 'done') return 'completed';
	return status;
};

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
			const list = Array.isArray(response.data) ? response.data : (response.data?.data ?? []);
			tasks.value = list.map((task) => ({ ...task, status: normalizeStatus(task.status) }));
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
			myTasks.value = Array.isArray(response.data) ? response.data.map((task) => ({
				...task,
				status: normalizeStatus(task.status),
			})) : [];
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
			selectedTask.value = tasks.value.find((task) => task.id == id) || null;
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

			tasks.value.push({ ...createdTask, status: normalizeStatus(createdTask.status) });
			hasLoadedAllTasks.value = true;
			return createdTask;
		} catch (err) {
			error.value = err?.response?.data?.message || err.message;
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
			const updatedTask = { ...response.data, status: normalizeStatus(response.data?.status) };

			const index = tasks.value.findIndex((task) => task.id === id);
			if (index !== -1) {
				tasks.value[index] = updatedTask;
			}
			if (selectedTask.value?.id === id) {
				selectedTask.value = updatedTask;
			}
			return updatedTask;
		} catch (err) {
			error.value = err?.response?.data?.message || err.message;
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
			tasks.value = tasks.value.filter((task) => task.id !== id);
			myTasks.value = myTasks.value.filter((task) => task.id !== id);
			if (selectedTask.value?.id === id) {
				selectedTask.value = null;
			}
		} catch (err) {
			error.value = err?.response?.data?.message || err.message;
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


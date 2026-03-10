<template>
  <section class="space-y-6">
    <header>
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">User Management</h1>
      <p class="text-slate-600 dark:text-slate-300">Create and manage system users.</p>
    </header>

    <form class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 space-y-6" @submit.prevent="createAdminUser">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Create User</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="space-y-2">
          <label for="new-user-name" class="text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
          <input id="new-user-name" v-model="newUserForm.name" type="text" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label for="new-user-department" class="text-sm font-medium text-slate-700 dark:text-slate-300">Department</label>
          <select id="new-user-department" v-model="newUserForm.department" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option disabled value="">Select department</option>
            <option v-for="department in departmentOptions" :key="department" :value="department">{{ department }}</option>
          </select>
        </div>

        <div class="space-y-2">
          <label for="new-user-email" class="text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
          <input id="new-user-email" v-model="newUserForm.email" type="email" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label for="new-user-role" class="text-sm font-medium text-slate-700 dark:text-slate-300">Role</label>
          <select id="new-user-role" v-model="newUserForm.role" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option value="admin">Admin</option>
            <option value="manager">Manager</option>
            <option value="member">Member</option>
          </select>
        </div>

        <div class="space-y-2">
          <label for="new-user-password" class="text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
          <input id="new-user-password" v-model="newUserForm.password" type="password" required minlength="8" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label for="new-user-password-confirm" class="text-sm font-medium text-slate-700 dark:text-slate-300">Confirm Password</label>
          <input id="new-user-password-confirm" v-model="newUserForm.password_confirmation" type="password" required minlength="8" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>
      </div>

      <div class="flex items-center gap-3">
        <input id="new-user-active" v-model="newUserForm.is_active" type="checkbox" class="h-4 w-4" />
        <label for="new-user-active" class="text-sm text-slate-700 dark:text-slate-200">Active user</label>
      </div>

      <div v-if="userMessage" class="rounded-xl border px-4 py-3 text-sm" :class="userMessageType === 'error' ? 'border-red-300 text-red-700 dark:border-red-700 dark:text-red-300' : 'border-emerald-300 text-emerald-700 dark:border-emerald-700 dark:text-emerald-300'">
        {{ userMessage }}
      </div>

      <div class="flex items-center justify-end">
        <button
          type="submit"
          :disabled="userCreating"
          class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ userCreating ? 'Creating...' : 'Create User' }}
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b border-slate-200 dark:border-slate-700 text-left">
              <th class="px-3 py-2 font-semibold text-slate-700 dark:text-slate-200">Name</th>
              <th class="px-3 py-2 font-semibold text-slate-700 dark:text-slate-200">Department</th>
              <th class="px-3 py-2 font-semibold text-slate-700 dark:text-slate-200">Email</th>
              <th class="px-3 py-2 font-semibold text-slate-700 dark:text-slate-200">Role</th>
              <th class="px-3 py-2 font-semibold text-slate-700 dark:text-slate-200">Status</th>
              <th class="px-3 py-2 font-semibold text-slate-700 dark:text-slate-200">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in adminUsers" :key="user.id" class="border-b border-slate-100 dark:border-slate-700/60">
              <td class="px-3 py-2 text-slate-700 dark:text-slate-200">{{ user.name }}</td>
              <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ user.department || '-' }}</td>
              <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ user.email || '-' }}</td>
              <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ user.role }}</td>
              <td class="px-3 py-2">
                <span :class="user.is_active ? 'text-emerald-600' : 'text-slate-500'">{{ user.is_active ? 'Active' : 'Inactive' }}</span>
              </td>
              <td class="px-3 py-2">
                <div class="flex items-center gap-2">
                  <button type="button" class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100" @click="openEditUserModal(user)">Edit</button>
                  <button type="button" class="rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 hover:bg-amber-100" :disabled="userActionLoading" @click="toggleUserActive(user)">
                    {{ user.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                  <button type="button" class="rounded-lg bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 hover:bg-red-100 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="userActionLoading || user.id === currentUserId" @click="deleteAdminUser(user)">Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!adminUsers.length">
              <td colspan="6" class="px-3 py-4 text-center text-slate-500 dark:text-slate-400">No users found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </form>

    <div v-if="showEditUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50" @click="closeEditUserModal"></div>
      <div class="relative w-full max-w-2xl rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 space-y-5">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Edit User</h3>
          <button type="button" class="text-slate-500 hover:text-slate-700" @click="closeEditUserModal">✕</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
            <input v-model="editUserForm.name" type="text" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Department</label>
            <select v-model="editUserForm.department" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
              <option disabled value="">Select department</option>
              <option v-if="editUserForm.department && !departmentOptions.includes(editUserForm.department)" :value="editUserForm.department">{{ editUserForm.department }}</option>
              <option v-for="department in departmentOptions" :key="department" :value="department">{{ department }}</option>
            </select>
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
            <input v-model="editUserForm.email" type="email" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Role</label>
            <select v-model="editUserForm.role" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
              <option value="admin">Admin</option>
              <option value="manager">Manager</option>
              <option value="member">Member</option>
            </select>
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">New Password (optional)</label>
            <input v-model="editUserForm.password" type="password" minlength="8" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Confirm New Password</label>
            <input v-model="editUserForm.password_confirmation" type="password" minlength="8" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
          </div>
        </div>

        <div class="flex items-center gap-3">
          <input id="edit-user-active" v-model="editUserForm.is_active" type="checkbox" class="h-4 w-4" />
          <label for="edit-user-active" class="text-sm text-slate-700 dark:text-slate-200">Active user</label>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" class="rounded-xl border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm" @click="closeEditUserModal">Cancel</button>
          <button type="button" :disabled="userActionLoading" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50" @click="saveEditedUser">
            {{ userActionLoading ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { adminUserService } from '../services/api';

const departmentOptions = [
  'ICT',
  'Big Data',
  'SMRP',
  'DSS',
  'DES',
  'Census',
  'Finance',
  'DDG Office',
  'DG office',
  'HCS',
  'HR',
  'Procurement',
  'SPIU Office',
];

const adminUsers = ref([]);
const userCreating = ref(false);
const userActionLoading = ref(false);
const userMessage = ref('');
const userMessageType = ref('success');
const showEditUserModal = ref(false);
const editingUserId = ref(null);
const currentUserId = Number(window.TaskAppAuth?.user?.id || 0);

const getFirstValidationError = (error, fallbackMessage) => {
  const validationErrors = error?.response?.data?.errors;
  if (validationErrors && typeof validationErrors === 'object') {
    const firstErrorGroup = Object.values(validationErrors)[0];
    const firstError = Array.isArray(firstErrorGroup) ? firstErrorGroup[0] : null;
    return firstError || fallbackMessage;
  }

  return error?.response?.data?.message || fallbackMessage;
};

const newUserForm = ref({
  name: '',
  department: '',
  email: '',
  role: 'member',
  password: '',
  password_confirmation: '',
  is_active: true,
});

const editUserForm = ref({
  name: '',
  department: '',
  email: '',
  role: 'member',
  password: '',
  password_confirmation: '',
  is_active: true,
});

const resetNewUserForm = () => {
  newUserForm.value = {
    name: '',
    department: '',
    email: '',
    role: 'member',
    password: '',
    password_confirmation: '',
    is_active: true,
  };
};

const loadAdminUsers = async () => {
  try {
    const response = await adminUserService.getAll();
    adminUsers.value = Array.isArray(response.data?.users) ? response.data.users : [];
  } catch {
    userMessageType.value = 'error';
    userMessage.value = 'Unable to load users list.';
  }
};

const createAdminUser = async () => {
  userCreating.value = true;
  userMessage.value = '';

  try {
    const payload = {
      name: newUserForm.value.name,
      department: newUserForm.value.department,
      email: newUserForm.value.email,
      role: newUserForm.value.role,
      password: newUserForm.value.password,
      password_confirmation: newUserForm.value.password_confirmation,
      is_active: newUserForm.value.is_active,
    };

    const response = await adminUserService.create(payload);
    userMessageType.value = 'success';
    userMessage.value = response.data?.message || 'User created successfully.';
    resetNewUserForm();
    await loadAdminUsers();
  } catch (error) {
    userMessage.value = getFirstValidationError(error, 'Failed to create user.');
    userMessageType.value = 'error';
  } finally {
    userCreating.value = false;
  }
};

const openEditUserModal = (user) => {
  editingUserId.value = user.id;
  editUserForm.value = {
    name: user.name || '',
    department: user.department || '',
    email: user.email || '',
    role: user.role || 'member',
    password: '',
    password_confirmation: '',
    is_active: Boolean(user.is_active),
  };
  showEditUserModal.value = true;
};

const closeEditUserModal = () => {
  showEditUserModal.value = false;
  editingUserId.value = null;
};

const saveEditedUser = async () => {
  if (!editingUserId.value) return;

  userActionLoading.value = true;
  userMessage.value = '';

  try {
    const payload = {
      name: editUserForm.value.name,
      department: editUserForm.value.department,
      email: editUserForm.value.email,
      role: editUserForm.value.role,
      password: editUserForm.value.password || undefined,
      password_confirmation: editUserForm.value.password_confirmation || undefined,
      is_active: editUserForm.value.is_active,
    };

    const response = await adminUserService.update(editingUserId.value, payload);
    userMessageType.value = 'success';
    userMessage.value = response.data?.message || 'User updated successfully.';
    closeEditUserModal();
    await loadAdminUsers();
  } catch (error) {
    userMessage.value = getFirstValidationError(error, 'Failed to update user.');
    userMessageType.value = 'error';
  } finally {
    userActionLoading.value = false;
  }
};

const toggleUserActive = async (user) => {
  userActionLoading.value = true;
  userMessage.value = '';

  try {
    const response = await adminUserService.toggleActive(user.id);
    userMessageType.value = 'success';
    userMessage.value = response.data?.message || 'User status updated.';
    await loadAdminUsers();
  } catch (error) {
    userMessageType.value = 'error';
    userMessage.value = error?.response?.data?.message || 'Failed to update user status.';
  } finally {
    userActionLoading.value = false;
  }
};

const deleteAdminUser = async (user) => {
  if (Number(user.id) === Number(currentUserId)) {
    userMessageType.value = 'error';
    userMessage.value = 'You cannot delete your own account from this screen.';
    return;
  }

  const confirmed = window.confirm(`Delete user ${user.name}?`);
  if (!confirmed) return;

  userActionLoading.value = true;
  userMessage.value = '';

  try {
    const response = await adminUserService.delete(user.id);
    userMessageType.value = 'success';
    userMessage.value = response.data?.message || 'User deleted successfully.';
    await loadAdminUsers();
  } catch (error) {
    userMessageType.value = 'error';
    userMessage.value = error?.response?.data?.message || 'Failed to delete user.';
  } finally {
    userActionLoading.value = false;
  }
};

onMounted(async () => {
  await loadAdminUsers();
});
</script>

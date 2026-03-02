<template>
  <section class="space-y-6">
    <header>
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Settings</h1>
      <p class="text-slate-600 dark:text-slate-300">Manage branding and email delivery settings.</p>
    </header>

    <form class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 space-y-6" @submit.prevent="saveBranding">
      <div class="space-y-2">
        <label for="app-name" class="text-sm font-medium text-slate-700 dark:text-slate-300">App Name</label>
        <input
          id="app-name"
          v-model="appName"
          type="text"
          maxlength="120"
          required
          class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100"
        />
      </div>

      <div class="space-y-2">
        <label for="app-logo" class="text-sm font-medium text-slate-700 dark:text-slate-300">Logo</label>
        <input
          id="app-logo"
          type="file"
          accept="image/*"
          class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100"
          @change="handleLogoChange"
        />
        <p class="text-xs text-slate-500 dark:text-slate-400">Accepted image files, up to 2MB.</p>
      </div>

      <div v-if="logoPreview" class="space-y-2">
        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Preview</p>
        <img :src="logoPreview" alt="App Logo Preview" class="h-16 w-16 rounded-xl border border-slate-200 dark:border-slate-700 object-cover" />
      </div>

      <div v-if="brandingMessage" class="rounded-xl border px-4 py-3 text-sm" :class="brandingMessageType === 'error' ? 'border-red-300 text-red-700 dark:border-red-700 dark:text-red-300' : 'border-emerald-300 text-emerald-700 dark:border-emerald-700 dark:text-emerald-300'">
        {{ brandingMessage }}
      </div>

      <div class="flex items-center justify-end">
        <button
          type="submit"
          :disabled="loading || brandingSaving"
          class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ brandingSaving ? 'Saving...' : 'Save Branding' }}
        </button>
      </div>
    </form>

    <form class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 space-y-6" @submit.prevent="saveReminderSettings">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Reminder Rules</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="space-y-2">
          <label for="reminder-frequency" class="text-sm font-medium text-slate-700 dark:text-slate-300">Frequency</label>
          <select id="reminder-frequency" v-model="reminderForm.frequency" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="every_n_days">Every N Days</option>
          </select>
        </div>

        <div v-if="reminderForm.frequency === 'every_n_days'" class="space-y-2">
          <label for="every-n" class="text-sm font-medium text-slate-700 dark:text-slate-300">Every N</label>
          <input id="every-n" v-model.number="reminderForm.every_n" type="number" min="1" max="365" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Rule Active</label>
          <label class="flex items-center gap-3 rounded-xl border border-slate-300 dark:border-slate-600 px-3 py-2">
            <input v-model="reminderForm.active" type="checkbox" class="h-4 w-4" />
            <span class="text-sm text-slate-700 dark:text-slate-200">Enable reminders</span>
          </label>
        </div>
      </div>

      <div v-if="reminderMessage" class="rounded-xl border px-4 py-3 text-sm" :class="reminderMessageType === 'error' ? 'border-red-300 text-red-700 dark:border-red-700 dark:text-red-300' : 'border-emerald-300 text-emerald-700 dark:border-emerald-700 dark:text-emerald-300'">
        {{ reminderMessage }}
      </div>

      <div class="flex items-center justify-end">
        <button
          type="submit"
          :disabled="loading || reminderSaving"
          class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ reminderSaving ? 'Saving...' : 'Save Reminder Rule' }}
        </button>
      </div>
    </form>

    <form class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 space-y-6" @submit.prevent="saveEmailSettings">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Email Settings</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label for="from-name" class="text-sm font-medium text-slate-700 dark:text-slate-300">Sender Name</label>
          <input id="from-name" v-model="emailForm.from_name" type="text" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label for="from-address" class="text-sm font-medium text-slate-700 dark:text-slate-300">Sender Email</label>
          <input id="from-address" v-model="emailForm.from_address" type="email" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label for="smtp-host" class="text-sm font-medium text-slate-700 dark:text-slate-300">SMTP Host</label>
          <input id="smtp-host" v-model="emailForm.smtp_host" type="text" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label for="smtp-port" class="text-sm font-medium text-slate-700 dark:text-slate-300">SMTP Port</label>
          <input id="smtp-port" v-model.number="emailForm.smtp_port" type="number" min="1" max="65535" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label for="smtp-username" class="text-sm font-medium text-slate-700 dark:text-slate-300">SMTP Username</label>
          <input id="smtp-username" v-model="emailForm.smtp_username" type="text" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label for="smtp-password" class="text-sm font-medium text-slate-700 dark:text-slate-300">SMTP Password</label>
          <input id="smtp-password" v-model="emailForm.smtp_password" type="password" placeholder="Leave empty to keep existing" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>

        <div class="space-y-2">
          <label for="smtp-encryption" class="text-sm font-medium text-slate-700 dark:text-slate-300">Encryption</label>
          <select id="smtp-encryption" v-model="emailForm.smtp_encryption" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option value="tls">TLS</option>
            <option value="ssl">SSL</option>
            <option value="none">None</option>
          </select>
        </div>

        <div class="space-y-2">
          <label for="smtp-test-to" class="text-sm font-medium text-slate-700 dark:text-slate-300">Test Recipient</label>
          <input id="smtp-test-to" v-model="testRecipient" type="email" placeholder="you@example.com" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>
      </div>

      <div v-if="emailMessage" class="rounded-xl border px-4 py-3 text-sm" :class="emailMessageType === 'error' ? 'border-red-300 text-red-700 dark:border-red-700 dark:text-red-300' : 'border-emerald-300 text-emerald-700 dark:border-emerald-700 dark:text-emerald-300'">
        {{ emailMessage }}
      </div>

      <div class="flex items-center justify-end gap-2">
        <button
          type="button"
          @click="sendTestEmail"
          :disabled="loading || emailSaving || emailTesting"
          class="rounded-xl border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ emailTesting ? 'Testing...' : 'Send SMTP Test' }}
        </button>
        <button
          type="submit"
          :disabled="loading || emailSaving || emailTesting"
          class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ emailSaving ? 'Saving...' : 'Save Email Settings' }}
        </button>
      </div>
    </form>

    <form class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 space-y-6" @submit.prevent="saveSecuritySettings">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Account & Security</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label for="timezone" class="text-sm font-medium text-slate-700 dark:text-slate-300">Timezone</label>
          <select id="timezone" v-model="securityForm.timezone" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option v-for="zone in timezoneOptions" :key="zone" :value="zone">{{ zone }}</option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium text-slate-700 dark:text-slate-300">HTTPS Enforcement</label>
          <label class="flex items-center gap-3 rounded-xl border border-slate-300 dark:border-slate-600 px-3 py-2">
            <input v-model="securityForm.enforce_https" type="checkbox" class="h-4 w-4" />
            <span class="text-sm text-slate-700 dark:text-slate-200">Force HTTPS redirects for web requests</span>
          </label>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="space-y-2">
          <label for="current-password" class="text-sm font-medium text-slate-700 dark:text-slate-300">Current Password</label>
          <input id="current-password" v-model="passwordForm.current_password" type="password" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>
        <div class="space-y-2">
          <label for="new-password" class="text-sm font-medium text-slate-700 dark:text-slate-300">New Password</label>
          <input id="new-password" v-model="passwordForm.password" type="password" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>
        <div class="space-y-2">
          <label for="confirm-password" class="text-sm font-medium text-slate-700 dark:text-slate-300">Confirm Password</label>
          <input id="confirm-password" v-model="passwordForm.password_confirmation" type="password" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </div>
      </div>

      <div v-if="securityMessage" class="rounded-xl border px-4 py-3 text-sm" :class="securityMessageType === 'error' ? 'border-red-300 text-red-700 dark:border-red-700 dark:text-red-300' : 'border-emerald-300 text-emerald-700 dark:border-emerald-700 dark:text-emerald-300'">
        {{ securityMessage }}
      </div>

      <div class="flex items-center justify-end gap-2">
        <button
          type="button"
          @click="changePassword"
          :disabled="loading || securitySaving || passwordSaving"
          class="rounded-xl border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ passwordSaving ? 'Updating...' : 'Change Password' }}
        </button>
        <button
          type="submit"
          :disabled="loading || securitySaving || passwordSaving"
          class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ securitySaving ? 'Saving...' : 'Save Security Settings' }}
        </button>
      </div>
    </form>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { settingsService } from '../services/api';

const appName = ref('TaskApp');
const logoFile = ref(null);
const logoPreview = ref('');
const loading = ref(false);
const brandingSaving = ref(false);
const brandingMessage = ref('');
const brandingMessageType = ref('success');

const emailForm = ref({
  from_name: '',
  from_address: '',
  smtp_host: '',
  smtp_port: 587,
  smtp_username: '',
  smtp_password: '',
  smtp_encryption: 'tls',
});
const testRecipient = ref('');
const emailSaving = ref(false);
const emailTesting = ref(false);
const emailMessage = ref('');
const emailMessageType = ref('success');

const reminderForm = ref({
  frequency: 'daily',
  every_n: 2,
  active: true,
});
const reminderSaving = ref(false);
const reminderMessage = ref('');
const reminderMessageType = ref('success');

const timezoneOptions = ref(['UTC']);
const securityForm = ref({
  timezone: 'UTC',
  enforce_https: false,
});
const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
});
const securitySaving = ref(false);
const passwordSaving = ref(false);
const securityMessage = ref('');
const securityMessageType = ref('success');

const loadBranding = async () => {
  loading.value = true;
  brandingMessage.value = '';

  try {
    const response = await settingsService.getBranding();
    appName.value = response.data?.app_name || 'TaskApp';
    logoPreview.value = response.data?.app_logo_url || '';
  } catch {
    brandingMessageType.value = 'error';
    brandingMessage.value = 'Unable to load current branding settings.';
  } finally {
    loading.value = false;
  }
};

const loadEmailSettings = async () => {
  try {
    const response = await settingsService.getEmailSettings();
    emailForm.value = {
      from_name: response.data?.from_name || '',
      from_address: response.data?.from_address || '',
      smtp_host: response.data?.smtp_host || '',
      smtp_port: response.data?.smtp_port || 587,
      smtp_username: response.data?.smtp_username || '',
      smtp_password: '',
      smtp_encryption: response.data?.smtp_encryption || 'tls',
    };
  } catch {
    emailMessageType.value = 'error';
    emailMessage.value = 'Unable to load current email settings.';
  }
};

const loadSecuritySettings = async () => {
  try {
    const response = await settingsService.getSecuritySettings();
    securityForm.value = {
      timezone: response.data?.timezone || 'UTC',
      enforce_https: Boolean(response.data?.enforce_https),
    };
  } catch {
    securityMessageType.value = 'error';
    securityMessage.value = 'Unable to load security settings.';
  }
};

const loadReminderSettings = async () => {
  try {
    const response = await settingsService.getReminderSettings();
    reminderForm.value = {
      frequency: response.data?.frequency || 'daily',
      every_n: response.data?.every_n || 2,
      active: Boolean(response.data?.active),
    };
  } catch {
    reminderMessageType.value = 'error';
    reminderMessage.value = 'Unable to load reminder settings.';
  }
};

const handleLogoChange = (event) => {
  const [file] = event.target.files || [];
  logoFile.value = file || null;

  if (file) {
    logoPreview.value = URL.createObjectURL(file);
  }
};

const saveBranding = async () => {
  brandingSaving.value = true;
  brandingMessage.value = '';

  try {
    const formData = new FormData();
    formData.append('app_name', appName.value);
    if (logoFile.value) {
      formData.append('app_logo', logoFile.value);
    }

    const response = await settingsService.updateBranding(formData);
    appName.value = response.data?.app_name || appName.value;
    logoPreview.value = response.data?.app_logo_url || logoPreview.value;
    logoFile.value = null;
    brandingMessageType.value = 'success';
    brandingMessage.value = response.data?.message || 'Branding settings saved.';
  } catch (error) {
    brandingMessageType.value = 'error';
    brandingMessage.value = error?.response?.data?.message || 'Failed to save branding settings.';
  } finally {
    brandingSaving.value = false;
  }
};

const saveEmailSettings = async () => {
  emailSaving.value = true;
  emailMessage.value = '';

  try {
    const response = await settingsService.updateEmailSettings(emailForm.value);
    emailForm.value.smtp_password = '';
    emailMessageType.value = 'success';
    emailMessage.value = response.data?.message || 'Email settings saved.';
  } catch (error) {
    emailMessageType.value = 'error';
    emailMessage.value = error?.response?.data?.message || 'Failed to save email settings.';
  } finally {
    emailSaving.value = false;
  }
};

const sendTestEmail = async () => {
  emailTesting.value = true;
  emailMessage.value = '';

  try {
    const response = await settingsService.testEmailSettings({ to: testRecipient.value });
    emailMessageType.value = 'success';
    emailMessage.value = response.data?.message || 'SMTP test email sent.';
  } catch (error) {
    emailMessageType.value = 'error';
    emailMessage.value = error?.response?.data?.message || 'SMTP test failed.';
  } finally {
    emailTesting.value = false;
  }
};

const saveReminderSettings = async () => {
  reminderSaving.value = true;
  reminderMessage.value = '';

  try {
    const response = await settingsService.updateReminderSettings(reminderForm.value);
    reminderMessageType.value = 'success';
    reminderMessage.value = response.data?.message || 'Reminder rule saved.';
  } catch (error) {
    reminderMessageType.value = 'error';
    reminderMessage.value = error?.response?.data?.message || 'Failed to save reminder rule.';
  } finally {
    reminderSaving.value = false;
  }
};

const saveSecuritySettings = async () => {
  securitySaving.value = true;
  securityMessage.value = '';

  try {
    const response = await settingsService.updateSecuritySettings(securityForm.value);
    securityMessageType.value = 'success';
    securityMessage.value = response.data?.message || 'Security settings saved.';
  } catch (error) {
    securityMessageType.value = 'error';
    securityMessage.value = error?.response?.data?.message || 'Failed to save security settings.';
  } finally {
    securitySaving.value = false;
  }
};

const changePassword = async () => {
  passwordSaving.value = true;
  securityMessage.value = '';

  try {
    const response = await settingsService.updatePassword(passwordForm.value);
    passwordForm.value = {
      current_password: '',
      password: '',
      password_confirmation: '',
    };
    securityMessageType.value = 'success';
    securityMessage.value = response.data?.message || 'Password updated.';
  } catch (error) {
    securityMessageType.value = 'error';
    securityMessage.value = error?.response?.data?.message || 'Failed to update password.';
  } finally {
    passwordSaving.value = false;
  }
};

onMounted(async () => {
  if (typeof Intl !== 'undefined' && typeof Intl.supportedValuesOf === 'function') {
    timezoneOptions.value = Intl.supportedValuesOf('timeZone');
  }

  await loadBranding();
  await loadReminderSettings();
  await loadEmailSettings();
  await loadSecuritySettings();
});
</script>

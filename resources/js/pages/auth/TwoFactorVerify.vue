<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
  code: ''
});

const submit = () => {
  form.post(route('2fa.validate'), {
    onFinish: () => {
      // Handle any logic after form submission, like redirecting if needed
    }
  });
};
</script>

<template>
  <div class="flex justify-center items-center min-h-screen bg-black-50">
    <div class="w-full max-w-sm bg-white p-8 rounded-lg shadow-lg">
      <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Two-Factor Authentication</h2>
      <p class="text-center text-gray-600 mb-6">Enter the 6-digit code from your authenticator app</p>

      <form @submit.prevent="submit">
        <!-- Input Field -->
        <div class="mb-4">
          <input
            v-model="form.code"
            type="text"
            placeholder="Enter Code"
            class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black"
          />
          <!-- Error Message -->
          <span v-if="form.errors.code" class="text-red-500 text-sm mt-2">{{ form.errors.code }}</span>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full mt-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          :disabled="form.processing"
        >
          Verify
        </button>
      </form>

      <!-- Loading Spinner -->
      <div v-if="form.processing" class="mt-4 text-center">
        <svg class="animate-spin h-6 w-6 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
          <path d="M4 12a8 8 0 0116 0" stroke="currentColor" stroke-width="4" class="opacity-75"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom styles for consistency with login page */
body {
  background-color: #f7fafc; /* Soft background color */
}

input:focus {
  border-color: #3182ce; /* Focused border color */
  box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.5); /* Subtle box shadow on focus */
}

button:disabled {
  background-color: #a0aec0; /* Disabled state */
  cursor: not-allowed;
}
</style>

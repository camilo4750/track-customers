<script setup>
import { ref, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { showToast } from '@/composables/useToast';
import { requireGuest } from '@/utils/authGuard';
import { router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Toast from 'primevue/toast';

const { login } = useAuth();
const toast = useToast();

onMounted(() => {
    requireGuest('/dashboard');
});

const formData = ref({
    email: '',
    password: ''
});

const isLoading = ref(false);

const handleLogin = async () => {
    if (!formData.value.email || !formData.value.password) {
        showToast(toast, {
            severity: 'warn',
            summary: 'Campos requeridos',
            detail: 'Por favor ingresa email y contraseña'
        });
        return;
    }

    isLoading.value = true;

    try {
        const response = await login(formData.value.email, formData.value.password);

        showToast(toast, {
            severity: 'success',
            summary: 'Login exitoso',
            detail: response.message || 'Bienvenido'
        });

        setTimeout(() => {
            router.visit('/dashboard');
        }, 1000);

    } catch (error) {
        const errorMessage = error.response?.data?.message || 'Error al iniciar sesión';
        const errors = error.response?.data?.errors || {};

        showToast(toast, {
            severity: 'error',
            summary: 'Error de autenticación',
            detail: errorMessage
        });

        console.error('Error en login:', errors);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <Toast />
        
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                    Iniciar Sesión
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Ingresa tus credenciales para acceder
                </p>
            </div>

            <form @submit.prevent="handleLogin" class="mt-8 space-y-6">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email
                        </label>
                        <InputText
                            id="email"
                            v-model="formData.email"
                            type="email"
                            placeholder="tu@email.com"
                            class="w-full"
                            :disabled="isLoading"
                            required
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Contraseña
                        </label>
                        <Password
                            id="password"
                            v-model="formData.password"
                            placeholder="••••••••"
                            :feedback="false"
                            toggleMask
                            class="w-full"
                            :disabled="isLoading"
                            required
                        />
                    </div>
                </div>

                <div>
                    <Button
                        type="submit"
                        label="Iniciar Sesión"
                        icon="pi pi-sign-in"
                        class="w-full"
                        :loading="isLoading"
                        :disabled="isLoading"
                    />
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        ¿No tienes cuenta?
                        <a href="/register" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>

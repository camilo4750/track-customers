<script setup>
import { computed, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { requireAuth } from '@/utils/authGuard';
import BaseLayout from '@/layouts/BaseLayout.vue';
import Card from 'primevue/card';
import Button from 'primevue/button';
import { router } from '@inertiajs/vue3';
import { ROLE_LABELS } from '../const/dashboard';

// Composables
const { getUserFromToken, isAuthenticated } = useAuth();

// Usuario actual desde el token
const currentUser = computed(() => getUserFromToken());

const displayRoles = computed(() => {
    const roles = currentUser.value?.roles[0];
    if (!roles) return 'No disponible';
    return ROLE_LABELS[roles];
});

// Proteger ruta
onMounted(() => {
    requireAuth();
});

// Navegación rápida
const quickLinks = [
    {
        title: 'Usuarios',
        description: 'Gestionar usuarios del sistema',
        icon: 'pi pi-users',
        route: '/users',
        color: 'bg-blue-500'
    },
    {
        title: 'Configuración',
        description: 'Ajustes del sistema',
        icon: 'pi pi-cog',
        route: '/settings',
        color: 'bg-gray-500'
    }
];

const navigateTo = (route) => {
    router.visit(route);
};
</script>

<template>
    <BaseLayout>
        <div class="w-full">
            <!-- Header de bienvenida -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    ¡Bienvenido, {{ currentUser?.name || 'Usuario' }}!
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Este es tu panel de control. Aquí puedes gestionar todo el sistema.
                </p>
            </div>

            <!-- Cards de acceso rápido -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card 
                    v-for="link in quickLinks" 
                    :key="link.title"
                    class="cursor-pointer hover:shadow-lg transition-shadow duration-200"
                    @click="navigateTo(link.route)"
                >
                    <template #header>
                        <div :class="[link.color, 'h-2 rounded-t-lg']"></div>
                    </template>
                    <template #title>
                        <div class="flex items-center gap-3">
                            <i :class="[link.icon, 'text-xl']"></i>
                            <span>{{ link.title }}</span>
                        </div>
                    </template>
                    <template #content>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ link.description }}
                        </p>
                    </template>
                    <template #footer>
                        <Button 
                            label="Ir" 
                            icon="pi pi-arrow-right" 
                            iconPos="right"
                            text
                            class="w-full"
                        />
                    </template>
                </Card>
            </div>

            <!-- Información del usuario -->
            <div class="mt-8">
                <Card>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-user"></i>
                            <span>Tu información</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nombre</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ currentUser?.name || 'No disponible' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ currentUser?.email || 'No disponible' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Rol</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ displayRoles }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                                <p class="font-medium text-green-600 dark:text-green-400">
                                    Activo
                                </p>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </BaseLayout>
</template>

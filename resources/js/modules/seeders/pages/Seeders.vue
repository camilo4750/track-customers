<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import BaseLayout from '@/layouts/BaseLayout.vue';
import Button from 'primevue/button';
import InputNumber from 'primevue/inputnumber';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { showToast } from '@/composables/useToast';
import { requireAuth, requireRole } from '@/utils/authGuard';

const toast = useToast();
const seedersList = ref([]);
const loading = ref(false);
const runningKey = ref(null);

const seedersByModule = computed(() => {
    const list = seedersList.value || [];
    const groups = {};
    list.forEach((s) => {
        const mod = s.module || 'Otros';
        if (!groups[mod]) groups[mod] = [];
        groups[mod].push(s);
    });
    return groups;
});

const getSeeders = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('seeders.list'));
        const list = response.data?.data ?? [];
        seedersList.value = list.map((s) => ({ ...s, _count: s.defaultCount }));
    } catch (error) {
        showToast(toast, {
            severity: 'error',
            summary: 'Error al cargar seeders',
            detail: error.response?.data?.message || 'No se pudo obtener la lista.',
        });
    } finally {
        loading.value = false;
    }
};

const runSeeder = async (key, count) => {
    runningKey.value = key;
    try {
        const response = await axios.post(route('seeders.run'), { key, count });
        const created = response.data?.data?.created;
        const message = response.data?.message || `Se crearon ${created ?? count} registros.`;
        showToast(toast, {
            severity: 'success',
            summary: 'Seeder ejecutado',
            detail: message,
        });
    } catch (error) {
        showToast(toast, {
            severity: 'error',
            summary: 'Error al ejecutar seeder',
            detail: error.response?.data?.message || 'No se pudo ejecutar el seeder.',
        });
    } finally {
        runningKey.value = null;
    }
};

onMounted(() => {
    requireAuth('/');
    requireRole(['admin'], '/');
    getSeeders();
});
</script>

<template>
    <BaseLayout>
        <div class="w-full">
            <div class="mb-6">
                <h1 class="text-xl font-semibold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400">
                    Datos de prueba
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Ejecuta seeders por módulo para generar datos de prueba. Solo disponible en entorno de desarrollo.
                </p>
            </div>

            <div v-if="loading" class="text-gray-500 dark:text-gray-400 text-sm">
                Cargando seeders...
            </div>

            <div v-else class="flex flex-col gap-6">
                <div
                    v-for="(seeders, moduleName) in seedersByModule"
                    :key="moduleName"
                    class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm"
                >
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ moduleName }}
                    </h2>
                    <div class="flex flex-col gap-4">
                        <div
                            v-for="seeder in seeders"
                            :key="seeder.key"
                            class="flex flex-wrap items-end gap-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3"
                        >
                            <div class="flex-1 min-w-[200px]">
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                    {{ seeder.description }}
                                </p>
                                <div class="flex flex-wrap items-center gap-3">
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Cantidad</label>
                                    <InputNumber
                                        :id="`count-${seeder.key}`"
                                        v-model="seeder._count"
                                        :min="1"
                                        :max="10000"
                                        :placeholder="String(seeder.defaultCount)"
                                        class="w-28 text-sm"
                                        show-buttons
                                    />
                                    <Button
                                        :label="runningKey === seeder.key ? 'Generando...' : 'Generar'"
                                        :loading="runningKey === seeder.key"
                                        :disabled="!!runningKey"
                                        size="small"
                                        @click="runSeeder(seeder.key, seeder._count ?? seeder.defaultCount)"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-if="Object.keys(seedersByModule).length === 0" class="text-gray-500 dark:text-gray-400 text-sm">
                    No hay seeders configurados.
                </p>
            </div>
        </div>
        <Toast />
    </BaseLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import Button from 'primevue/button';
import Select from 'primevue/select';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faXmark } from '@fortawesome/free-solid-svg-icons';
import { ROLES, STATUSES, DEFAULT_FILTERS } from '../const/user';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    filters: {
        type: Object,
        default: () => ({ ...DEFAULT_FILTERS })
    }
});

const emit = defineEmits(['update:modelValue', 'update:filters', 'apply', 'clear']);

// Estado local de los filtros
const localFilters = ref({ ...props.filters });

// Sincronizar cuando se abre el panel (modelValue cambia a true)
watch(() => props.modelValue, (isOpen) => {
    if (isOpen) {
        // Cuando se abre el panel, sincronizar con los filtros actuales
        localFilters.value = { ...props.filters };
    }
});

// Sincronizar cuando cambien los props
watch(() => props.filters, (newFilters) => {
    localFilters.value = { ...newFilters };
}, { deep: true });

// Cerrar panel
const closePanel = () => {
    emit('update:modelValue', false);
};

// Limpiar filtros
const clearFilters = () => {
    localFilters.value = { ...DEFAULT_FILTERS };
    emit('update:filters', { ...localFilters.value });
    emit('clear');
};

// Aplicar filtros
const applyFilters = () => {
    emit('update:filters', { ...localFilters.value });
    emit('apply', { ...localFilters.value });
    closePanel();
};
</script>

<template>
    <Transition name="slide-fade-left">
        <div
            v-if="modelValue"
            class="fixed left-0 top-0 h-full w-full max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col z-50"
        >
            <!-- Header del panel -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Filtros Avanzados
                </h2>
                <Button
                    rounded
                    text
                    severity="secondary"
                    size="small"
                    @click="closePanel"
                    class="h-8 w-8 p-0"
                >
                    <FontAwesomeIcon :icon="faXmark" class="text-sm" />
                </Button>
            </div>

            <!-- Contenido del panel de filtros -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="space-y-4">
                    <!-- Filtro por Rol -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Rol
                        </label>
                        <Select
                            v-model="localFilters.role"
                            :options="ROLES"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Todos los roles"
                            class="w-full text-sm"
                        />
                    </div>

                    <!-- Filtro por Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Estado
                        </label>
                        <Select
                            v-model="localFilters.status"
                            :options="STATUSES"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Todos los estados"
                            class="w-full text-sm"
                        />
                    </div>
                </div>
            </div>

            <!-- Footer del panel con botones -->
            <div class="flex items-center justify-end gap-2 p-4 border-t border-gray-200 dark:border-gray-700">
                <Button
                    label="Limpiar"
                    variant="outlined"
                    size="small"
                    @click="clearFilters"
                    class="h-9 px-4 text-sm"
                />
                <Button
                    label="Aplicar"
                    size="small"
                    @click="applyFilters"
                    class="h-9 px-4 text-sm"
                />
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.slide-fade-left-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-left-leave-active {
    transition: all 0.2s ease-in;
}

.slide-fade-left-enter-from {
    transform: translateX(-100%);
    opacity: 0;
}

.slide-fade-left-leave-to {
    transform: translateX(-100%);
    opacity: 0;
}
</style>

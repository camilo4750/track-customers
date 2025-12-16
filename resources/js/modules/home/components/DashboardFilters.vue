<script setup>
    import DatePicker from 'primevue/datepicker';
    import Select from 'primevue/select';
    import Button from 'primevue/button';
    
    const props = defineProps({
        clients: {
            type: Array,
            default: () => []
        }
    });
    
    const emit = defineEmits(['search']);
    
    const dateRange = defineModel('dateRange', { default: null });
    const selectedClient = defineModel('selectedClient', { default: null });
    
    const handleSearch = () => {
        emit('search', {
            dateRange: dateRange.value,
            clientId: selectedClient.value?.id || null,
            clientName: selectedClient.value?.name || null
        });
    };
    </script>
    
    <template>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <!-- Filtro de rango de fechas -->
                <div class="w-full lg:w-80">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rango de fechas
                    </label>
                    <DatePicker
                        v-model="dateRange"
                        selectionMode="range"
                        dateFormat="dd/mm/yy"
                        placeholder="Selecciona un rango de fechas"
                        class="w-full"
                        showIcon
                    />
                </div>
    
                <!-- Select de cliente -->
                <div class="w-full lg:w-96">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cliente
                    </label>
                    <Select
                        v-model="selectedClient"
                        :options="clients"
                        optionLabel="name"
                        placeholder="Selecciona un cliente"
                        class="w-full"
                        :clearable="true"
                    >
                        <template #value="slotProps">
                            <div v-if="slotProps.value" class="flex items-center">
                                <span>{{ slotProps.value.name }}</span>
                            </div>
                            <span v-else>{{ slotProps.placeholder }}</span>
                        </template>
                        <template #option="slotProps">
                            <div class="flex items-center">
                                <div>
                                    <div class="font-medium">{{ slotProps.option.name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ slotProps.option.email }}</div>
                                </div>
                            </div>
                        </template>
                    </Select>
                </div>
    
                <div class="w-full md:w-auto">
                    <Button
                        label="Buscar"
                        icon="pi pi-search"
                        @click="handleSearch"
                        class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                    />
                </div>
            </div>
        </div>
    </template>
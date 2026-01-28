<script setup>
import { ref, computed, watch, shallowRef } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Select from 'primevue/select';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import MultiSelect from 'primevue/multiselect';
import Popover from 'primevue/popover';
import Tag from 'primevue/tag';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faSearch, faFileExcel, faColumns, faFilterCircleXmark, faFilter } from '@fortawesome/free-solid-svg-icons';
import { formatDate } from '../utils/formatters';

const props = defineProps({
    data: {
        type: Array,
        required: true,
        default: () => []
    },
    columns: {
        type: Array,
        required: true,
        default: () => []
    },
    defaultColumns: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    },
    searchPlaceholder: {
        type: String,
        default: 'Buscar...'
    },
    emptyMessage: {
        type: String,
        default: 'No se encontraron registros.'
    },
    loadingMessage: {
        type: String,
        default: 'Cargando...'
    },
    dataKey: {
        type: String,
        default: 'id'
    },
    rows: {
        type: Number,
        default: 10
    },
    rowsPerPageOptions: {
        type: Array,
        default: () => [5, 10, 20, 50]
    },
    showSearch: {
        type: Boolean,
        default: true
    },
    showColumnSelector: {
        type: Boolean,
        default: true
    },
    showExport: {
        type: Boolean,
        default: true
    },
    showFilters: {
        type: Boolean,
        default: true
    },
    cellFormatters: {
        type: Object,
        default: () => ({})
    },
    /**
     * Habilitar filtros por columna individual
     */
    showColumnFilters: {
        type: Boolean,
        default: false
    },
    /**
     * Columnas que deben tener filtros habilitados
     * Array de strings (nombres de campos) o objetos con configuración
     * { field: string, type: 'text'|'number'|'date', matchMode?: string, operator?: string }
     */
    filterableColumns: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['action', 'filter', 'export', 'clear-filters', 'open-filters']);

// Estado interno
// Inicializar filtros con estructura base
// PrimeVue espera que filters sea un objeto reactivo con estructura específica
const filters = ref({ global: { value: null, matchMode: 'contains' } });
const selectedColumns = ref([...props.defaultColumns]);
const dt = ref();
// Refs para los Popovers de cada columna (usar Map para mejor manejo)
const popovers = shallowRef(new Map());

// Operadores para números (constante fuera del componente para mejor rendimiento)
const NUMERIC_OPERATORS = [
    { label: 'Igual (=)', value: 'equals', symbol: '=' },
    { label: 'Mayor que (>)', value: 'gt', symbol: '>' },
    { label: 'Menor que (<)', value: 'lt', symbol: '<' },
    { label: 'Mayor o igual (≥)', value: 'gte', symbol: '≥' },
    { label: 'Menor o igual (≤)', value: 'lte', symbol: '≤' },
    { label: 'Diferente (≠)', value: 'notEquals', symbol: '≠' }
];

// Mapa de configuraciones de filtro memoizado (computed para mejor rendimiento)
const filterConfigMap = computed(() => {
    if (!props.showColumnFilters || !props.filterableColumns || !Array.isArray(props.filterableColumns)) {
        return new Map();
    }
    
    const map = new Map();
    props.filterableColumns.forEach(col => {
        const field = typeof col === 'string' ? col : (col && col.field ? col.field : null);
        if (field) {
            const config = typeof col === 'object' && col !== null ? col : { field };
            
            // El tipo debe estar definido explícitamente en FILTERABLE_COLUMNS
            if (!config.type) {
                console.warn(`Column ${field} is filterable but type is not defined in filterableColumns. Defaulting to 'text'.`);
            }
            
            map.set(field, {
                type: config.type || 'text',
                matchMode: config.matchMode || (config.type === 'number' ? 'equals' : 'contains'),
                operator: config.operator || 'equals',
                operators: config.operators || NUMERIC_OPERATORS
            });
        }
    });
    
    return map;
});

// Set de columnas filtrables memoizado (computed para mejor rendimiento)
const filterableFieldsSet = computed(() => {
    if (!props.showColumnFilters || !props.filterableColumns || !Array.isArray(props.filterableColumns)) {
        return new Set();
    }
    
    return new Set(
        props.filterableColumns.map(col => {
            return typeof col === 'string' ? col : (col && col.field ? col.field : null);
        }).filter(Boolean)
    );
});

// Obtener configuración de filtro para una columna (ahora usa el mapa memoizado)
const getColumnFilterConfig = (field) => {
    return filterConfigMap.value.get(field) || null;
};

// Verificar si una columna tiene filtro habilitado (ahora usa el Set memoizado)
const isColumnFilterable = (field) => {
    return filterableFieldsSet.value.has(field);
};

// Verificar si un filtro está activo (helper para evitar repetir lógica en template)
const isFilterActive = (field) => {
    const filter = filters.value[field];
    return filter?.value !== null && filter?.value !== undefined && filter?.value !== '';
};

// Helper para crear función de ref que capture el field correctamente
const createPopoverRef = (field) => {
    return (el) => {
        if (el) {
            // Solo actualizar si el elemento no existe o es diferente
            const currentPopover = popovers.value.get(field);
            if (currentPopover !== el) {
                const newMap = new Map(popovers.value);
                newMap.set(field, el);
                popovers.value = newMap;
            }
        } else {
            // Solo eliminar si existe
            if (popovers.value.has(field)) {
                const newMap = new Map(popovers.value);
                newMap.delete(field);
                popovers.value = newMap;
            }
        }
    };
};

// Toggle del panel de filtro para una columna
const toggleFilterPanel = (event, field) => {
    const popover = popovers.value.get(field);
    if (popover) {
        popover.toggle(event);
    } else {
        console.warn(`Popover for field "${field}" not found. Available fields:`, Array.from(popovers.value.keys()));
    }
};

// Helper para crear estructura de filtro inicial
const createFilterStructure = (field) => {
    const config = getColumnFilterConfig(field);
    if (!config) return null;
    
    return {
        value: null,
        matchMode: config.matchMode
    };
};

// Aplicar filtro de columna
const applyColumnFilter = (field) => {
    if (dt.value && filters.value[field]) {
        const filterValue = filters.value[field].value;
        const matchMode = filters.value[field].matchMode || 'contains';
        dt.value.filter(filterValue, field, matchMode);
    }
};

// Inicializar columnas seleccionadas
if (props.defaultColumns.length > 0) {
    selectedColumns.value = [...props.defaultColumns];
} else if (props.columns.length > 0) {
    selectedColumns.value = [...props.columns];
}

// Inicializar filtros por columna de forma reactiva
// Observar solo los props, no el computed, para evitar ciclos infinitos
watch(() => [props.showColumnFilters, props.filterableColumns], (newVal, oldVal) => {
    // Evitar ejecutar si showColumnFilters es false o filterableColumns está vacío
    if (!props.showColumnFilters || !props.filterableColumns || !Array.isArray(props.filterableColumns) || props.filterableColumns.length === 0) {
        return;
    }
    
    // Crear un nuevo objeto para evitar mutaciones directas que puedan causar problemas
    const newFilters = { ...filters.value };
    let hasChanges = false;
    
    props.filterableColumns.forEach(col => {
        const field = typeof col === 'string' ? col : (col && col.field ? col.field : null);
        if (field && !newFilters[field]) {
            const filterStruct = createFilterStructure(field);
            if (filterStruct) {
                newFilters[field] = filterStruct;
                hasChanges = true;
            }
        }
    });
    
    // Solo actualizar si hay cambios para evitar actualizaciones innecesarias
    if (hasChanges) {
        filters.value = newFilters;
    }
}, { immediate: true, deep: false });

// Manejar cambio de columnas visibles
const onToggle = (value) => {
    selectedColumns.value = value.length > 0 
        ? value 
        : (props.defaultColumns.length > 0 ? [...props.defaultColumns] : [...props.columns]);
};

// Exportar a CSV
const exportCSV = () => {
    if (dt.value) {
        dt.value.exportCSV();
        emit('export');
    }
};

// Limpiar filtros
const clearFilter = () => {
    // Crear nuevo objeto para evitar mutaciones directas
    const newFilters = { ...filters.value };
    
    // Resetear filtro global
    newFilters.global = { value: null, matchMode: 'contains' };
    
    // Limpiar también filtros por columna si están habilitados
    if (props.showColumnFilters && props.filterableColumns && Array.isArray(props.filterableColumns)) {
        props.filterableColumns.forEach(col => {
            const field = typeof col === 'string' ? col : (col && col.field ? col.field : null);
            if (field && newFilters[field]) {
                const filterStruct = createFilterStructure(field);
                if (filterStruct) {
                    newFilters[field] = filterStruct;
                }
            }
        });
        
        // Aplicar limpieza a PrimeVue DataTable
        if (dt.value) {
            props.filterableColumns.forEach(col => {
                const field = typeof col === 'string' ? col : (col && col.field ? col.field : null);
                if (field) {
                    dt.value.filter(null, field, 'contains');
                }
            });
        }
    }
    
    // Actualizar filters de una sola vez
    filters.value = newFilters;
    
    emit('clear-filters');
};

// Ejecutar búsqueda
const performSearch = () => {
    emit('filter', filters.value);
};

// Abrir panel de filtros
const openFilterPanel = () => {
    emit('open-filters');
};

// Manejar acción
const handleAction = (action, row) => {
    emit('action', { action, row });
};

// Formatear celda usando formatters personalizados o por defecto
const formatCell = (field, value, row) => {
    // Si hay un formatter personalizado, usarlo
    if (props.cellFormatters[field]) {
        return props.cellFormatters[field](value, row);
    }
    
    // Formatters por defecto para campos comunes
    if (field === 'created_at' || field === 'updated_at') {
        return formatDate(value);
    }
    
    return value;
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 backdrop-blur-sm">
        <!-- Barra de herramientas superior -->
        <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-t-xl">
            <!-- Botones izquierda -->
            <div class="flex items-center gap-2 flex-shrink-0 order-1">
                <slot name="toolbar-left">
                    <!-- Si no hay slot personalizado, mostrar botones por defecto -->
                    <template v-if="!$slots['toolbar-left']">
                        <Button
                            v-if="showFilters"
                            rounded
                            outlined
                            severity="info"
                            size="small"
                            @click="openFilterPanel"
                            title="Abrir panel de filtros"
                            class="h-9 w-9 p-0"
                        >
                            <FontAwesomeIcon :icon="faFilter" class="text-xs" />
                        </Button>
                        <Button
                            v-if="showFilters"
                            rounded
                            outlined
                            severity="info"
                            size="small"
                            @click="clearFilter"
                            title="Limpiar filtros tabla"
                            class="h-9 w-9 p-0"
                        >
                            <FontAwesomeIcon :icon="faFilterCircleXmark" class="text-xs" />
                        </Button>
                    </template>
                </slot>
            </div>

            <div v-if="showSearch" class="flex-1 flex justify-center items-center gap-2 min-w-0 order-2 sm:order-2">
                <IconField iconPosition="left" class="w-full max-w-md">
                    <InputIcon>
                        <FontAwesomeIcon :icon="faSearch" class="text-gray-400 text-xs" />
                    </InputIcon>
                    <InputText
                        v-model="filters['global'].value"
                        :placeholder="searchPlaceholder"
                        class="w-full text-sm h-9"
                        @keyup.enter="performSearch"
                    />
                </IconField>
                <Button
                    rounded
                    outlined
                    severity="info"
                    size="small"
                    @click="performSearch"
                    title="Buscar"
                    class="h-9 w-9 p-0 flex-shrink-0"
                >
                    <FontAwesomeIcon :icon="faSearch" class="text-xs" />
                </Button>
            </div>

            <!-- Botones derecha -->
            <div class="flex items-center gap-2 flex-shrink-0 order-3 sm:order-3">
                <slot name="toolbar-right">
                    <MultiSelect
                        v-if="showColumnSelector"
                        v-model="selectedColumns"
                        :options="columns"
                        optionLabel="header"
                        display="chip"
                        placeholder="Columnas"
                        :maxSelectedLabels="2"
                        class="w-full sm:w-48 text-sm"
                        @update:modelValue="onToggle"
                    >
                        <template #value="slotProps">
                            <div v-if="slotProps.value && slotProps.value.length" class="flex items-center gap-1 text-xs">
                                <FontAwesomeIcon :icon="faColumns" class="mr-1 text-xs" />
                                <span>{{ slotProps.value.length }} columnas</span>
                            </div>
                            <span v-else class="text-xs">Seleccionar columnas</span>
                        </template>
                    </MultiSelect>
                    <Button
                        v-if="showExport"
                        rounded
                        outlined
                        severity="success"
                        size="small"
                        @click="exportCSV"
                        title="Exportar a Excel"
                        class="h-9 w-9 p-0"
                    >
                        <FontAwesomeIcon :icon="faFileExcel" class="text-xs" />
                    </Button>
                </slot>
            </div>
        </div>

        <!-- Tabla -->
        <div class="p-4">
            <DataTable
                ref="dt"
                v-model:filters="filters"
                :value="data"
                :loading="loading"
                paginator
                :rows="rows"
                :rowsPerPageOptions="rowsPerPageOptions"
                :dataKey="dataKey"
                :filterDisplay="'menu'"
                scrollable
                scrollHeight="600px"
                stripedRows
                showGridlines
                removableSort
                class="text-sm"
            >
                <template #empty>
                    <div class="text-center py-6 text-gray-500 dark:text-gray-400 text-sm">
                        {{ emptyMessage }}
                    </div>
                </template>

                <template #loading>
                    <div class="text-center py-6 text-sm">
                        {{ loadingMessage }}
                    </div>
                </template>

                <!-- Columna de acciones (solo si hay slot) -->
                <Column 
                    v-if="$slots.actions"
                    header="Acciones" 
                    :exportable="false" 
                    style="min-width: 120px" 
                    bodyStyle="text-align: center; padding: 0.5rem;"
                >
        
                    <template #body="slotProps">
                        <slot name="actions" :row="slotProps.data" :handleAction="handleAction" />
                    </template>
                </Column>  

                <!-- Columnas dinámicas basadas en selectedColumns -->
                <Column
                    v-for="col of selectedColumns"
                    :key="col.field"
                    :field="col.field"
                    :sortable="col.sortable !== false"
                    :style="col.style || { minWidth: col.field === 'id' ? '60px' : '120px' }"
                    :headerStyle="col.headerStyle || 'font-size: 0.75rem; font-weight: 600; padding: 0.5rem;'"
                    :bodyStyle="col.bodyStyle || 'font-size: 0.75rem; padding: 0.5rem;'"
                    :filterField="isColumnFilterable(col.field) ? col.field : undefined"
                    :showFilterMenu="false"
                >
                    <!-- Header personalizado con botón de filtro -->
                    <template #header>
                        <div class="flex items-center justify-between gap-2">
                            <span>{{ col.header }}</span>
                            <template v-if="isColumnFilterable(col.field)">
                                <div class="relative">
                                    <Button
                                        rounded
                                        text
                                        severity="secondary"
                                        size="small"
                                        @click="(event) => toggleFilterPanel(event, col.field)"
                                        :class="{ 'text-blue-600 dark:text-blue-400': isFilterActive(col.field) }"
                                        class="h-6 w-6 p-0"
                                        v-tooltip.top="'Filtrar'"
                                    >
                                        <FontAwesomeIcon :icon="faFilter" class="text-xs" />
                                    </Button>
                                    <Popover
                                        :ref="createPopoverRef(col.field)"
                                        :id="`filter-${col.field}`"
                                        class="p-3"
                                        style="min-width: 250px;"
                                    >
                                        <template v-if="isColumnFilterable(col.field)">
                                            <slot 
                                                :name="`filter-${col.field}`"
                                                :filterModel="filters[col.field]"
                                                :filterCallback="() => applyColumnFilter(col.field)"
                                                :field="col.field"
                                                :column="col"
                                                :config="getColumnFilterConfig(col.field)"
                                            >
                                                <!-- Memoizar config para evitar múltiples llamadas -->
                                                <template v-if="(() => {
                                                    const config = getColumnFilterConfig(col.field);
                                                    return config?.type === 'number';
                                                })()">
                                                    <div class="space-y-2">
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                                            Operador
                                                        </label>
                                                        <Select
                                                            v-model="filters[col.field].matchMode"
                                                            :options="(() => {
                                                                const config = getColumnFilterConfig(col.field);
                                                                return config?.operators || NUMERIC_OPERATORS;
                                                            })()"
                                                            optionLabel="label"
                                                            optionValue="value"
                                                            placeholder="Seleccionar operador"
                                                            class="w-full text-xs"
                                                            @change="() => applyColumnFilter(col.field)"
                                                        />
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mt-2">
                                                            Valor
                                                        </label>
                                                        <InputNumber
                                                            v-model="filters[col.field].value"
                                                            :placeholder="`Filtrar ${col.header.toLowerCase()}...`"
                                                            class="w-full text-xs"
                                                            :min="0"
                                                            :useGrouping="false"
                                                            @input="() => applyColumnFilter(col.field)"
                                                        />
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="space-y-2">
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                                            {{ col.header }}
                                                        </label>
                                                        <InputText
                                                            v-model="filters[col.field].value"
                                                            type="text"
                                                            :placeholder="`Filtrar por ${col.header.toLowerCase()}...`"
                                                            class="w-full text-xs"
                                                            @input="() => applyColumnFilter(col.field)"
                                                        />
                                                    </div>
                                                </template>
                                            </slot>
                                        </template>
                                    </Popover>
                                </div>
                            </template>
                        </div>
                    </template>


                    <template #body="slotProps">
                        <!-- Slot personalizado para cada campo -->
                        <slot 
                            :name="`cell-${col.field}`" 
                            :value="slotProps.data[col.field]" 
                            :row="slotProps.data"
                            :field="col.field"
                        >
                            <!-- Si no hay slot, usar formatter o mostrar valor por defecto -->
                            <span class="text-xs">{{ formatCell(col.field, slotProps.data[col.field], slotProps.data) }}</span>
                        </slot>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
</template>


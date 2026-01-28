<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import BaseLayout from '../../../layouts/BaseLayout.vue';
import Toast from 'primevue/toast';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEdit, faUserShield, faPlus } from '@fortawesome/free-solid-svg-icons';
import { formatDate } from '../../../utils/formatters';
import UserForm from '../components/UserForm.vue';
import EditUserForm from '../components/EditUserForm.vue';
import UserFilters from '../components/UserFilters.vue';
import AdvancedDataTable from '../../../components/AdvancedDataTable.vue';
import { useUserFilters } from '../composables/useUserFilters';
import {
    ROLES,
    STATUSES,
    COLUMNS,
    DEFAULT_COLUMNS,
    FILTERABLE_COLUMNS
} from '../const/user';
import { useToast } from 'primevue/usetoast';
import { showToast } from '../../../composables/useToast';
import { useUserPanel } from '../composables/useUserPanel';

const props = defineProps({
    data: {
        type: Array,
        default: () => []
    }
});

const toast = useToast();
const users = ref(props.data);
const loading = ref(false);

const { 
    showCreatePanel, 
    showEditPanel,
    editingUser,
    openCreatePanel,
    closeCreatePanel,
    openEditPanel,
    closeEditPanel,
} = useUserPanel();

const {
    showFilterPanel,
    advancedFilters,
    openFilterPanel,
    clearFilter,
    applyAdvancedFilters,
    clearAdvancedFilters
} = useUserFilters();


const getStatusSeverity = (status) => {
    switch (status) {
        case 'active':
            return 'success';
        case 'inactive':
            return 'danger';
        default:
            return null;
    }
};

const getRoleSeverity = (role) => {
    switch (role) {
        case 'admin':
            return 'danger';
        case 'user':
            return 'info';
        default:
            return null;
    }
};

const getUsers = async () => {
    loading.value = true;
    try {
        const filters = {};
        
        if (advancedFilters.value.role) {
            filters.role = advancedFilters.value.role;
        }
        
        if (advancedFilters.value.status) {
            filters.status = advancedFilters.value.status;
        }

        const response = await axios.get(route('users.indexApi'), { params: filters });
        
        if (response.status === 200) {
            users.value = response.data?.data;
        }
    } catch (error) {
        showToast(toast, {
            severity: 'error',
            summary: 'Error al obtener usuarios',
            detail: error.response?.data?.message
        });
    } finally {
        loading.value = false;
    }
};

const handleUserCreated = async () => {
    closeCreatePanel();
    closeEditPanel();
    await getUsers();
};

const handleEdit = (user) => {
    editingUser.value = user;
    openEditPanel();
};

const handleApplyFilters = async () => {
    applyAdvancedFilters(advancedFilters.value);
    await getUsers();
};

const handleRoles = (user) => {
    console.log('Gestionar roles de usuario:', user);
    // Aquí se implementará la lógica de roles en el futuro
};

</script>

<template>
    <BaseLayout>
        <div class="w-full">
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-4 gap-3">
                <h1 class="text-xl font-semibold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400">
                    Gestión de Usuarios
                </h1>
                <Button
                    label="Crear Usuario"
                    size="small"
                    @click="openCreatePanel"
                    class="h-9 px-4 text-sm"
                >
                    <FontAwesomeIcon :icon="faPlus" class="mr-1 text-xs" />
                    Crear Usuario
                </Button>
            </div>

            <AdvancedDataTable
                :data="users"
                :columns="COLUMNS"
                :defaultColumns="DEFAULT_COLUMNS"
                :loading="loading"
                :searchPlaceholder="'Buscar usuarios...'"
                :emptyMessage="'No se encontraron usuarios.'"
                :loadingMessage="'Cargando usuarios...'"
                :showColumnFilters="true"
                :filterableColumns="FILTERABLE_COLUMNS"
                dataKey="id"
                @open-filters="openFilterPanel"
                @clear-filters="clearFilter"
            >
                <!-- Filtro personalizado para role (select) -->
                <template #filter-role="{ filterModel, filterCallback }">
                    <Select
                        v-model="filterModel.value"
                        :options="[{ label: 'Todos', value: null }, ...ROLES]"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Todos los roles"
                        class="w-full text-xs"
                        @change="filterCallback()"
                    />
                </template>

                <!-- Filtro personalizado para status (select) -->
                <template #filter-status="{ filterModel, filterCallback }">
                    <Select
                        v-model="filterModel.value"
                        :options="[{ label: 'Todos', value: null }, ...STATUSES]"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Todos los estados"
                        class="w-full text-xs"
                        @change="filterCallback()"
                    />
                </template>

                <!-- Slot para celda de status -->
                <template #cell-status="{ value, row }">
                    <Tag
                        :value="value === 'active' ? 'Activo' : 'Inactivo'"
                        :severity="getStatusSeverity(value)"
                        class="text-xs px-2 py-0.5"
                    />
                </template>

                <!-- Slot para celda de role -->
                <template #cell-role="{ value, row }">
                    <Tag
                        :value="value"
                        :severity="getRoleSeverity(value)"
                        class="text-xs px-2 py-0.5"
                    />
                </template>

                <!-- Slot para celda de email_verified_at -->
                <template #cell-email_verified_at="{ value, row }">
                    <span v-if="value">
                        <Tag value="Verificado" severity="success" class="text-xs px-2 py-0.5" />
                    </span>
                    <span v-else>
                        <Tag value="No verificado" severity="warning" class="text-xs px-2 py-0.5" />
                    </span>
                </template>

                <!-- Slot para celda de created_at -->
                <template #cell-created_at="{ value, row }">
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ formatDate(value) }}</span>
                </template>

                <!-- Slot para celda de updated_at -->
                <template #cell-updated_at="{ value, row }">
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ formatDate(value) }}</span>
                </template>

                <!-- Slot para acciones -->
                <template #actions="{ row }">
                    <div class="flex justify-center gap-1.5">
                        <Button
                            rounded
                            outlined
                            severity="info"
                            size="small"
                            @click="handleEdit(row)"
                            title="Editar usuario"
                            class="h-7 w-7 p-0"
                        >
                            <FontAwesomeIcon :icon="faEdit" class="text-xs" />
                        </Button>
                        <Button
                            rounded
                            outlined
                            severity="warning"
                            size="small"
                            @click="handleRoles(row)"
                            title="Gestionar roles"
                            class="h-7 w-7 p-0"
                        >
                            <FontAwesomeIcon :icon="faUserShield" class="text-xs" />
                        </Button>
                    </div>
                </template>
            </AdvancedDataTable>
        </div>

        <!-- Panel lateral izquierdo para filtros -->
        <UserFilters
            v-model="showFilterPanel"
            v-model:filters="advancedFilters"
            @apply="handleApplyFilters"
            @clear="clearAdvancedFilters"
        />

        <!-- Panel lateral para crear usuario -->
        <UserForm
            v-model="showCreatePanel"
            @success="handleUserCreated"
            @cancel="closeCreatePanel"
        />

        <!-- Panel lateral para editar usuario -->
        <EditUserForm
            v-model="showEditPanel"
            :editingUser="editingUser"
            @success="handleUserCreated"
            @cancel="closeEditPanel"
        />
    </BaseLayout>
    <Toast />
</template>

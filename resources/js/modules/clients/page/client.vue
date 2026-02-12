<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import BaseLayout from '../../../layouts/BaseLayout.vue';
import Toast from 'primevue/toast';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEdit, faPlus, faTrash } from '@fortawesome/free-solid-svg-icons';
import { formatDate } from '../../../utils/formatters';
import { useAuth } from '../../../composables/useAuth';
import ClientForm from '../components/ClientForm.vue';
import EditClientForm from '../components/EditClientForm.vue';
import ClientFilters from '../components/ClientFilters.vue';
import AdvancedDataTable from '../../../components/AdvancedDataTable.vue';
import { useClientFilters } from '../composables/useClientFilters';
import {
    STATUSES,
    COLUMNS,
    DEFAULT_COLUMNS,
    FILTERABLE_COLUMNS
} from '../const/client';
import { useToast } from 'primevue/usetoast';
import { showToast } from '../../../composables/useToast';
import { useClientPanel } from '../composables/useClientPanel';
import { useModal } from '../../../composables/useModal';
import ConfirmDeleteClientModal from '../components/ConfirmDeleteClientModal.vue';
import { requireAuth } from '@/utils/authGuard';

const toast = useToast();
const clients = ref([]);
const loading = ref(false);

const { openModal } = useModal();
const { getUserFromToken } = useAuth();

const {
    showCreatePanel,
    showEditPanel,
    editingClient,
    openCreatePanel,
    closeCreatePanel,
    openEditPanel,
    closeEditPanel,
} = useClientPanel();

const {
    showFilterPanel,
    advancedFilters,
    openFilterPanel,
    clearFilter,
    applyAdvancedFilters,
    clearAdvancedFilters
} = useClientFilters();

const isAdmin = computed(() => {
    const user = getUserFromToken();
    return user?.roles?.includes('admin') ?? false;
});

function normalizeTagsForDisplay(tags) {
    if (Array.isArray(tags)) return tags;
    if (typeof tags === 'string') {
        try {
            return JSON.parse(tags || '[]');
        } catch {
            return [];
        }
    }
    return [];
}

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

const statusFilterOptions = [{ label: 'Todos', value: null }, ...STATUSES];

const getClients = async () => {
    loading.value = true;
    try {
        const filters = {};
        if (advancedFilters.value.status) {
            filters.status = advancedFilters.value.status;
        }
        const response = await axios.get(route('clients.indexApi'), { params: filters });
        if (response.status === 200) {
            clients.value = response.data?.data ?? [];
        }
    } catch (error) {
        showToast(toast, {
            severity: 'error',
            summary: 'Error al obtener clientes',
            detail: error.response?.data?.message
        });
    } finally {
        loading.value = false;
    }
};

const handleClientCreated = async () => {
    closeCreatePanel();
    closeEditPanel();
    await getClients();
};

const handleEdit = (client) => {
    editingClient.value = client;
    openEditPanel();
};

const handleApplyFilters = async () => {
    applyAdvancedFilters(advancedFilters.value);
    await getClients();
};

const handleDelete = (client) => {
    openModal({
        title: 'Eliminar cliente',
        component: ConfirmDeleteClientModal,
        props: { client },
        size: 'sm',
        closable: false,
        dismissableMask: false,
        onConfirm: async () => {
            try {
                loading.value = true;
                const response = await axios.delete(route('clients.destroy', client.id));
                showToast(toast, {
                    severity: 'success',
                    summary: 'Cliente eliminado',
                    detail: response.data?.message || 'El cliente ha sido eliminado correctamente.'
                });
                await getClients();
            } catch (error) {
                showToast(toast, {
                    severity: 'error',
                    summary: 'Error al eliminar cliente',
                    detail: error.response?.data?.message || 'No se pudo eliminar el cliente.'
                });
            } finally {
                loading.value = false;
            }
        },
    });
};

onMounted(() => {
    requireAuth('/');
    getClients();
});
</script>

<template>
    <BaseLayout>
        <div class="w-full">
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-4 gap-3">
                <h1 class="text-xl font-semibold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400">
                    Gestión de Clientes
                </h1>
                <Button
                    label="Crear Cliente"
                    size="small"
                    @click="openCreatePanel"
                    class="h-9 px-4 text-sm"
                >
                    <FontAwesomeIcon :icon="faPlus" class="mr-1 text-xs" />
                    Crear Cliente
                </Button>
            </div>

            <AdvancedDataTable
                :data="clients"
                :columns="COLUMNS"
                :defaultColumns="DEFAULT_COLUMNS"
                :loading="loading"
                :searchPlaceholder="'Buscar clientes...'"
                :emptyMessage="'No se encontraron clientes.'"
                :loadingMessage="'Cargando clientes...'"
                :showColumnFilters="true"
                :filterableColumns="FILTERABLE_COLUMNS"
                dataKey="id"
                @open-filters="openFilterPanel"
                @clear-filters="clearFilter"
            >
                <template #filter-status="{ filterModel, filterCallback }">
                    <Select
                        v-model="filterModel.value"
                        :options="statusFilterOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Todos los estados"
                        class="w-full text-xs"
                        @change="filterCallback()"
                    />
                </template>

                <template #cell-status="{ value }">
                    <Tag
                        :value="value === 'active' ? 'Activo' : 'Inactivo'"
                        :severity="getStatusSeverity(value)"
                        class="text-xs px-2 py-0.5"
                    />
                </template>

                <template #cell-tags="{ value }">
                    <span class="text-xs text-gray-600 dark:text-gray-400">
                        {{ normalizeTagsForDisplay(value).join(', ') || '—' }}
                    </span>
                </template>

                <template #cell-created_at="{ value }">
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ formatDate(value) }}</span>
                </template>

                <template #cell-updated_at="{ value }">
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ formatDate(value) }}</span>
                </template>

                <template #actions="{ row }">
                    <div class="flex justify-center gap-1.5">
                        <Button
                            rounded
                            outlined
                            severity="info"
                            size="small"
                            @click="handleEdit(row)"
                            title="Editar cliente"
                            class="h-7 w-7 p-0"
                        >
                            <FontAwesomeIcon :icon="faEdit" class="text-xs" />
                        </Button>
                        <Button
                            v-if="isAdmin"
                            rounded
                            outlined
                            severity="danger"
                            size="small"
                            @click="handleDelete(row)"
                            title="Eliminar cliente"
                            class="h-7 w-7 p-0"
                        >
                            <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                        </Button>
                    </div>
                </template>
            </AdvancedDataTable>
        </div>

        <ClientFilters
            v-model="showFilterPanel"
            v-model:filters="advancedFilters"
            @apply="handleApplyFilters"
            @clear="clearAdvancedFilters"
        />

        <ClientForm
            v-model="showCreatePanel"
            @success="handleClientCreated"
            @cancel="closeCreatePanel"
        />

        <EditClientForm
            v-model="showEditPanel"
            :editingClient="editingClient"
            @success="handleClientCreated"
            @cancel="closeEditPanel"
        />
    </BaseLayout>
    <Toast />
</template>

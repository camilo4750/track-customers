<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import BaseLayout from '@/layouts/BaseLayout.vue';
import Toast from 'primevue/toast';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEdit, faPlus, faTrash } from '@fortawesome/free-solid-svg-icons';
import { formatDate, formatPrice } from '@/utils/formatters';
import { useAuth } from '@/composables/useAuth';
import { useToast } from 'primevue/usetoast';
import { showToast } from '@/composables/useToast';
import { requireAuth } from '@/utils/authGuard';
import { useModal } from '@/composables/useModal';
import AdvancedDataTable from '@/components/AdvancedDataTable.vue';
import ProductForm from '../components/ProductForm.vue';
import EditProductForm from '../components/EditProductForm.vue';
import ProductFilters from '../components/ProductFilters.vue';
import ConfirmDeleteProductModal from '../components/ConfirmDeleteProductModal.vue';
import { useProductPanel } from '../composables/useProductPanel';
import { useProductFilters } from '../composables/useProductFilters';
import {
    COLUMNS,
    DEFAULT_COLUMNS,
    FILTERABLE_COLUMNS
} from '../const/product';

const toast = useToast();
const products = ref([]);
const loading = ref(false);

const { openModal } = useModal();
const { getUserFromToken } = useAuth();

const {
    showCreatePanel,
    showEditPanel,
    editingProduct,
    openCreatePanel,
    closeCreatePanel,
    openEditPanel,
    closeEditPanel,
} = useProductPanel();

const {
    showFilterPanel,
    advancedFilters,
    openFilterPanel,
    clearFilter,
    applyAdvancedFilters,
    clearAdvancedFilters
} = useProductFilters();

const isAdmin = computed(() => {
    const user = getUserFromToken();
    return user?.roles?.includes('admin') ?? false;
});

const getProducts = async () => {
    loading.value = true;
    try {
        const params = {};
        if (advancedFilters.value.category) {
            params.category = advancedFilters.value.category;
        }
        const response = await axios.get(route('products.indexApi'), { params });
        if (response.status === 200) {
            products.value = response.data?.data ?? [];
        }
    } catch (error) {
        showToast(toast, {
            severity: 'error',
            summary: 'Error al obtener productos',
            detail: error.response?.data?.message
        });
    } finally {
        loading.value = false;
    }
};

const handleProductCreated = async () => {
    closeCreatePanel();
    closeEditPanel();
    await getProducts();
};

const handleEdit = (product) => {
    editingProduct.value = product;
    openEditPanel();
};

const handleApplyFilters = async () => {
    applyAdvancedFilters(advancedFilters.value);
    await getProducts();
};

const handleDelete = (product) => {
    openModal({
        title: 'Eliminar producto',
        component: ConfirmDeleteProductModal,
        props: { product },
        size: 'sm',
        closable: false,
        dismissableMask: false,
        onConfirm: async () => {
            try {
                loading.value = true;
                const response = await axios.delete(route('products.destroy', product.id));
                showToast(toast, {
                    severity: 'success',
                    summary: 'Producto eliminado',
                    detail: response.data?.message || 'El producto ha sido eliminado correctamente.'
                });
                await getProducts();
            } catch (error) {
                showToast(toast, {
                    severity: 'error',
                    summary: 'Error al eliminar producto',
                    detail: error.response?.data?.message || 'No se pudo eliminar el producto.'
                });
            } finally {
                loading.value = false;
            }
        },
    });
};

onMounted(() => {
    requireAuth('/');
    getProducts();
});
</script>

<template>
    <BaseLayout>
        <div class="w-full">
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-4 gap-3">
                <h1 class="text-xl font-semibold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400">
                    Gestión de Productos
                </h1>
                <Button
                    label="Crear Producto"
                    size="small"
                    @click="openCreatePanel"
                    class="h-9 px-4 text-sm"
                >
                    <FontAwesomeIcon :icon="faPlus" class="mr-1 text-xs" />
                    Crear Producto
                </Button>
            </div>

            <AdvancedDataTable
                :data="products"
                :columns="COLUMNS"
                :defaultColumns="DEFAULT_COLUMNS"
                :loading="loading"
                :searchPlaceholder="'Buscar productos...'"
                :emptyMessage="'No se encontraron productos.'"
                :loadingMessage="'Cargando productos...'"
                :showColumnFilters="true"
                :filterableColumns="FILTERABLE_COLUMNS"
                dataKey="id"
                @open-filters="openFilterPanel"
                @clear-filters="clearFilter"
            >
                <template #cell-price="{ value }">
                    <span class="text-xs text-gray-600 dark:text-gray-400">
                        {{ formatPrice(value) }}
                    </span>
                </template>

                <template #cell-category="{ value }">
                    <span class="text-xs text-gray-600 dark:text-gray-400">
                        {{ value || '—' }}
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
                            title="Editar producto"
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
                            title="Eliminar producto"
                            class="h-7 w-7 p-0"
                        >
                            <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                        </Button>
                    </div>
                </template>
            </AdvancedDataTable>
        </div>

        <ProductFilters
            v-model="showFilterPanel"
            v-model:filters="advancedFilters"
            @apply="handleApplyFilters"
            @clear="clearAdvancedFilters"
        />

        <ProductForm
            v-model="showCreatePanel"
            @success="handleProductCreated"
            @cancel="closeCreatePanel"
        />

        <EditProductForm
            v-model="showEditPanel"
            :editingProduct="editingProduct"
            @success="handleProductCreated"
            @cancel="closeEditPanel"
        />
    </BaseLayout>
    <Toast />
</template>

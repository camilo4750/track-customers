import { ref } from 'vue';
import axios from 'axios';
import { DEFAULT_PRODUCT } from '../const/product';
import { useToast } from 'primevue/usetoast';
import { showToast } from '@/composables/useToast';

export function useProductForm(onSuccess) {
    const toast = useToast();
    const newProduct = ref({ ...DEFAULT_PRODUCT });
    const editingProduct = ref(null);

    const errors = ref({});
    const submitting = ref(false);

    const isEditing = () => editingProduct.value !== null;

    const loadProductForEdit = (product) => {
        if (product) {
            editingProduct.value = product;
            newProduct.value = {
                name: product.name || '',
                sku: product.sku || '',
                price: product.price != null ? Number(product.price) : null,
                category: product.category || ''
            };
        }
    };

    const resetForm = () => {
        newProduct.value = { ...DEFAULT_PRODUCT };
        errors.value = {};
        editingProduct.value = null;
    };

    const validateForm = () => {
        errors.value = {};
        let isValid = true;

        if (!newProduct.value.name || newProduct.value.name.trim() === '') {
            errors.value.name = 'El nombre es requerido';
            isValid = false;
        } else if (newProduct.value.name.length > 255) {
            errors.value.name = 'El nombre debe tener menos de 255 caracteres';
            isValid = false;
        }

        if (!newProduct.value.sku || newProduct.value.sku.trim() === '') {
            errors.value.sku = 'El SKU es requerido';
            isValid = false;
        } else if (newProduct.value.sku.length > 100) {
            errors.value.sku = 'El SKU debe tener menos de 100 caracteres';
            isValid = false;
        }

        if (newProduct.value.price === null || newProduct.value.price === '' || Number.isNaN(Number(newProduct.value.price))) {
            errors.value.price = 'El precio es requerido';
            isValid = false;
        } else {
            const priceNum = Number(newProduct.value.price);
            if (priceNum < 0) {
                errors.value.price = 'El precio no puede ser negativo';
                isValid = false;
            }
        }

        if (newProduct.value.category && newProduct.value.category.length > 100) {
            errors.value.category = 'La categoría debe tener menos de 100 caracteres';
            isValid = false;
        }

        return isValid;
    };

    const createProduct = async () => {
        if (!validateForm()) return;

        submitting.value = true;
        errors.value = {};

        try {
            const dataForm = {
                name: newProduct.value.name.trim(),
                sku: newProduct.value.sku.trim(),
                price: Number(newProduct.value.price),
                category: newProduct.value.category?.trim() || null
            };

            const response = await axios.post(route('products.store'), dataForm);

            if (response.status === 200) {
                showToast(toast, {
                    severity: 'success',
                    summary: 'Producto creado',
                    detail: response.data?.message
                });
                resetForm();
                onSuccess();
            }
        } catch (error) {
            showToast(toast, {
                severity: 'error',
                summary: 'Error',
                detail: error.response?.data?.message || 'Error al crear el producto'
            });
            errors.value = error.response?.data?.errors || {};
            errors.value.general = error.response?.data?.message;
        } finally {
            submitting.value = false;
        }
    };

    const updateProduct = async () => {
        if (!validateForm()) return;

        if (!editingProduct.value) {
            showToast(toast, {
                severity: 'error',
                summary: 'Error',
                detail: 'No se ha seleccionado un producto para editar'
            });
            return;
        }

        submitting.value = true;
        errors.value = {};

        try {
            const dataForm = {
                name: newProduct.value.name.trim(),
                sku: newProduct.value.sku.trim(),
                price: Number(newProduct.value.price),
                category: newProduct.value.category?.trim() || null
            };

            const response = await axios.put(
                route('products.update', { id: editingProduct.value.id }),
                dataForm
            );

            if (response.status === 200) {
                showToast(toast, {
                    severity: 'success',
                    summary: 'Producto actualizado',
                    detail: response.data?.message
                });
                resetForm();
                onSuccess();
            }
        } catch (error) {
            showToast(toast, {
                severity: 'error',
                summary: 'Error',
                detail: error.response?.data?.message || 'Error al actualizar el producto'
            });
            errors.value = error.response?.data?.errors || {};
            errors.value.general = error.response?.data?.message;
        } finally {
            submitting.value = false;
        }
    };

    return {
        newProduct,
        editingProduct,
        errors,
        submitting,
        isEditing,
        resetForm,
        loadProductForEdit,
        validateForm,
        createProduct,
        updateProduct,
    };
}

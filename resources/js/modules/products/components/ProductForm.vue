<script setup>
import { useProductForm } from '../composables/useProductForm';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Message from 'primevue/message';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faXmark } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue', 'success', 'cancel']);

const {
    newProduct,
    errors,
    submitting,
    resetForm,
    createProduct: createProductHandler,
} = useProductForm(() => {
    emit('success');
    closeForm();
});

const closeForm = () => {
    emit('update:modelValue', false);
    resetForm();
};

const handleCancel = () => {
    closeForm();
    emit('cancel');
};

const createProduct = async () => {
    await createProductHandler();
};
</script>

<template>
    <Transition name="slide-fade">
        <div
            v-if="modelValue"
            class="fixed right-0 top-0 h-full w-full max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col z-50"
        >
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Crear Nuevo Producto
                </h2>
                <Button
                    rounded
                    text
                    severity="secondary"
                    size="small"
                    @click="handleCancel"
                    class="h-8 w-8 p-0"
                >
                    <FontAwesomeIcon :icon="faXmark" class="text-sm" />
                </Button>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
                <form @submit.prevent="createProduct" class="space-y-4">
                    <Message
                        v-if="errors.general"
                        severity="error"
                        :closable="false"
                        class="text-sm"
                    >
                        {{ errors.general }}
                    </Message>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <InputText
                            v-model="newProduct.name"
                            placeholder="Ingrese el nombre"
                            class="w-full text-sm h-9"
                            :class="{ 'p-invalid': errors.name }"
                        />
                        <small v-if="errors.name" class="p-error text-xs mt-1 block">
                            {{ errors.name }}
                        </small>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <InputText
                            v-model="newProduct.sku"
                            placeholder="Ej: PROD-001"
                            class="w-full text-sm h-9"
                            :class="{ 'p-invalid': errors.sku }"
                        />
                        <small v-if="errors.sku" class="p-error text-xs mt-1 block">
                            {{ errors.sku }}
                        </small>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Precio <span class="text-red-500">*</span>
                        </label>
                        <InputNumber
                            v-model="newProduct.price"
                            mode="decimal"
                            :min="0"
                            :minFractionDigits="0"
                            :maxFractionDigits="2"
                            placeholder="0"
                            class="w-full text-sm"
                            inputClass="w-full h-9"
                            :class="{ 'p-invalid': errors.price }"
                        />
                        <small v-if="errors.price" class="p-error text-xs mt-1 block">
                            {{ errors.price }}
                        </small>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Categoría
                        </label>
                        <InputText
                            v-model="newProduct.category"
                            placeholder="Opcional"
                            class="w-full text-sm h-9"
                            :class="{ 'p-invalid': errors.category }"
                        />
                        <small v-if="errors.category" class="p-error text-xs mt-1 block">
                            {{ errors.category }}
                        </small>
                    </div>
                </form>
            </div>

            <div class="flex items-center justify-end gap-2 p-4 border-t border-gray-200 dark:border-gray-700">
                <Button
                    label="Cancelar"
                    variant="outlined"
                    size="small"
                    @click="handleCancel"
                    class="h-9 px-4 text-sm"
                    :disabled="submitting"
                />
                <Button
                    label="Crear"
                    size="small"
                    @click="createProduct"
                    class="h-9 px-4 text-sm"
                    :loading="submitting"
                    :disabled="submitting"
                />
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s ease-in;
}

.slide-fade-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(100%);
    opacity: 0;
}
</style>

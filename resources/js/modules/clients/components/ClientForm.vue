<script setup>
import { useClientForm } from '../composables/useClientForm';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Chips from 'primevue/chips';
import Message from 'primevue/message';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faXmark } from '@fortawesome/free-solid-svg-icons';
import { STATUSES } from '../const/client';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue', 'success', 'cancel']);

const {
    newClient,
    errors,
    submitting,
    resetForm,
    createClient: createClientHandler,
} = useClientForm(() => {
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

const createClient = async () => {
    await createClientHandler();
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
                    Crear Nuevo Cliente
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
                <form @submit.prevent="createClient" class="space-y-4">
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
                            v-model="newClient.name"
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
                            Email <span class="text-red-500">*</span>
                        </label>
                        <InputText
                            v-model="newClient.email"
                            type="email"
                            placeholder="cliente@ejemplo.com"
                            class="w-full text-sm h-9"
                            :class="{ 'p-invalid': errors.email }"
                        />
                        <small v-if="errors.email" class="p-error text-xs mt-1 block">
                            {{ errors.email }}
                        </small>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <InputText
                            v-model="newClient.phone"
                            placeholder="Ej: +56 9 1234 5678"
                            class="w-full text-sm h-9"
                            :class="{ 'p-invalid': errors.phone }"
                        />
                        <small v-if="errors.phone" class="p-error text-xs mt-1 block">
                            {{ errors.phone }}
                        </small>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Estado
                        </label>
                        <Select
                            v-model="newClient.status"
                            :options="STATUSES"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Seleccione un estado"
                            class="w-full text-sm"
                            :class="{ 'p-invalid': errors.status }"
                        />
                        <small v-if="errors.status" class="p-error text-xs mt-1 block">
                            {{ errors.status }}
                        </small>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Tags <span class="text-red-500">*</span>
                        </label>
                        <Chips
                            v-model="newClient.tags"
                            placeholder="Escriba y presione Enter"
                            separator=","
                            class="w-full"
                            :class="{ 'p-invalid': errors.tags }"
                        />
                        <small class="text-gray-500 text-xs mt-1 block">
                            Máximo 50 caracteres por tag. Separe con coma o Enter.
                        </small>
                        <small v-if="errors.tags" class="p-error text-xs mt-1 block">
                            {{ errors.tags }}
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
                    @click="createClient"
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

<script setup>
import { watch } from 'vue';
import { useUserForm } from '../composables/useUserForm';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Select from 'primevue/select';
import Message from 'primevue/message';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faXmark } from '@fortawesome/free-solid-svg-icons';
import { ROLES, STATUSES } from '../const/user';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    editingUser: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['update:modelValue', 'success', 'cancel']);

// Usar el composable para el formulario (el mismo que para crear)
const {
    newUser,
    errors,
    submitting,
    resetForm,
    loadUserForEdit,
    updateUser: updateUserHandler,
} = useUserForm(() => {
    // Callback cuando se actualiza exitosamente
    emit('success');
    closeForm();
});

// Observar cambios en editingUser prop
watch(() => props.editingUser, (userData) => {
    if (userData) {
        loadUserForEdit(userData);
    }
}, { immediate: true });

// Cerrar formulario
const closeForm = () => {
    emit('update:modelValue', false);
    resetForm();
};

// Manejar cancelar
const handleCancel = () => {
    closeForm();
    emit('cancel');
};

// Actualizar usuario
const updateUser = async () => {
    await updateUserHandler();
};

</script>

<template>
    <Transition name="slide-fade">
        <div
            v-if="modelValue"
            class="fixed right-0 top-0 h-full w-full max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col z-50"
        >
            <!-- Header del panel -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Editar Usuario
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

            <!-- Contenido del formulario -->
            <div class="flex-1 overflow-y-auto p-4">
                <form @submit.prevent="updateUser" class="space-y-4">
                    <!-- Mensaje de error general -->
                    <Message
                        v-if="errors.general"
                        severity="error"
                        :closable="false"
                        class="text-sm"
                    >
                        {{ errors.general }}
                    </Message>

                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <InputText
                            v-model="newUser.name"
                            placeholder="Ingrese el nombre"
                            class="w-full text-sm h-9"
                            :class="{ 'p-invalid': errors.name }"
                        />
                        <small v-if="errors.name" class="p-error text-xs mt-1 block">
                            {{ errors.name }}
                        </small>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <InputText
                            v-model="newUser.email"
                            type="email"
                            placeholder="usuario@ejemplo.com"
                            class="w-full text-sm h-9"
                            :class="{ 'p-invalid': errors.email }"
                        />
                        <small v-if="errors.email" class="p-error text-xs mt-1 block">
                            {{ errors.email }}
                        </small>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Contraseña
                            <span class="text-gray-500 text-xs">(dejar vacío para mantener la actual)</span>
                        </label>
                        <Password
                            v-model="newUser.password"
                            placeholder="Dejar vacío para mantener la actual"
                            :feedback="false"
                            toggleMask
                            class="w-full"
                            inputClass="text-sm h-9 w-full"
                            :class="{ 'p-invalid': errors.password }"
                        />
                        <small v-if="errors.password" class="p-error text-xs mt-1 block">
                            {{ errors.password }}
                        </small>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div v-if="newUser.password">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Confirmar Contraseña
                        </label>
                        <Password
                            v-model="newUser.password_confirmation"
                            placeholder="Confirme la contraseña"
                            :feedback="false"
                            toggleMask
                            class="w-full"
                            inputClass="text-sm h-9 w-full"
                            :class="{ 'p-invalid': errors.password_confirmation }"
                        />
                        <small v-if="errors.password_confirmation" class="p-error text-xs mt-1 block">
                            {{ errors.password_confirmation }}
                        </small>
                    </div>

                    <!-- Rol -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Rol
                        </label>
                        <Select
                            v-model="newUser.role"
                            :options="ROLES"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Seleccione un rol"
                            class="w-full text-sm"
                            :class="{ 'p-invalid': errors.role }"
                        />
                        <small v-if="errors.role" class="p-error text-xs mt-1 block">
                            {{ errors.role }}
                        </small>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Estado
                        </label>
                        <Select
                            v-model="newUser.status"
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
                </form>
            </div>

            <!-- Footer del panel con botones -->
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
                    label="Actualizar"
                    size="small"
                    @click="updateUser"
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


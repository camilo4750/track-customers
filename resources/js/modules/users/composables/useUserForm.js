import { ref } from 'vue';
import axios from 'axios';
import { DEFAULT_USER } from '../const/user';
import { useToast } from 'primevue/usetoast';
import { showToast } from '../../../composables/useToast';

export function useUserForm(onSuccess) {
    const toast = useToast()
    const newUser = ref({ ...DEFAULT_USER });
    const editingUser = ref(null);

    const errors = ref({});
    const submitting = ref(false);

    // Determinar si estamos en modo edición
    const isEditing = () => editingUser.value !== null;

    // Cargar datos de usuario para editar
    const loadUserForEdit = (user) => {
        if (user) {
            editingUser.value = user;
            newUser.value = {
                name: user.name || '',
                email: user.email || '',
                password: '', // No cargar contraseña
                password_confirmation: '',
                role: user.role || 'user',
                status: user.status || 'active'
            };
        }
    };

    // Resetear formulario
    const resetForm = () => {
        newUser.value = { ...DEFAULT_USER };
        errors.value = {};
        editingUser.value = null;
    };

    // Validar formulario
    const validateForm = () => {
        errors.value = {};
        let isValid = true;

        if (!newUser.value.name || newUser.value.name.trim() === '') {
            errors.value.name = 'El nombre es requerido';
            isValid = false;
        }

        if (!newUser.value.email || newUser.value.email.trim() === '') {
            errors.value.email = 'El email es requerido';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newUser.value.email)) {
            errors.value.email = 'El email no es válido';
            isValid = false;
        }

        // Validación de contraseña según el modo
        if (isEditing()) {
            // Editar: contraseña opcional, pero si se proporciona debe ser válida
            if (newUser.value.password && newUser.value.password.length > 0) {
                if (newUser.value.password.length < 8) {
                    errors.value.password = 'La contraseña debe tener al menos 8 caracteres';
                    isValid = false;
                }
                if (newUser.value.password !== newUser.value.password_confirmation) {
                    errors.value.password_confirmation = 'Las contraseñas no coinciden';
                    isValid = false;
                }
            }
        } else {
            // Crear: contraseña obligatoria
            if (!newUser.value.password || newUser.value.password.length < 8) {
                errors.value.password = 'La contraseña debe tener al menos 8 caracteres';
                isValid = false;
            }
            if (newUser.value.password !== newUser.value.password_confirmation) {
                errors.value.password_confirmation = 'Las contraseñas no coinciden';
                isValid = false;
            }
        }

        return isValid;
    };

    // Crear usuario
    const createUser = async () => {
        if (!validateForm()) {
            return;
        }

        submitting.value = true;
        errors.value = {};

        try {
            const dataForm = {
                name: newUser.value.name,
                email: newUser.value.email,
                password: newUser.value.password,
                password_confirmation: newUser.value.password_confirmation,
                role: newUser.value.role,
                status: newUser.value.status
            };

            const response = await axios.post(route('users.store'), dataForm);

            if (response.status === 200) {
                const message = response.data?.message;
                showToast(toast, {
                    severity: 'success',
                    summary: 'Usuario creado',
                    detail: message
                });

                resetForm();
                onSuccess();
            }
        } catch (error) {
            showToast(toast, {
                severity: 'error',
                summary: 'Error',
                detail: error.response?.data?.message || 'Error al crear el usuario'
            });
            errors.value = error.response?.data?.errors || {};
            errors.value.general = error.response?.data?.message;
        } finally {
            submitting.value = false;
        }
    };

    // Actualizar usuario
    const updateUser = async () => {
        if (!validateForm()) {
            return;
        }

        if (!editingUser.value) {
            showToast(toast, {
                severity: 'error',
                summary: 'Error',
                detail: 'No se ha seleccionado un usuario para editar'
            });
            return;
        }

        submitting.value = true;
        errors.value = {};

        try {
            const dataForm = {
                id: editingUser.value.id,
                name: newUser.value.name,
                email: newUser.value.email,
                role: newUser.value.role,
                status: newUser.value.status
            };

            // Solo incluir contraseña si se proporcionó una nueva
            if (newUser.value.password && newUser.value.password.length > 0) {
                dataForm.password = newUser.value.password;
                dataForm.password_confirmation = newUser.value.password_confirmation;
            }

            const response = await axios.put(route('users.update', { id: editingUser.value.id }), dataForm);

            if (response.status === 200) {
                const message = response.data?.message;
                showToast(toast, {
                    severity: 'success',
                    summary: 'Usuario actualizado',
                    detail: message
                });

                resetForm();
                onSuccess();
            }
        } catch (error) {
            showToast(toast, {
                severity: 'error',
                summary: 'Error',
                detail: error.response?.data?.message || 'Error al actualizar el usuario'
            });
            errors.value = error.response?.data?.errors || {};
            errors.value.general = error.response?.data?.message;
        } finally {
            submitting.value = false;
        }
    };

    return {
        newUser,
        editingUser,
        errors,
        submitting,
        isEditing,
        resetForm,
        loadUserForEdit,
        validateForm,
        createUser,
        updateUser,
    };
}

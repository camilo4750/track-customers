import { ref } from 'vue';
import axios from 'axios';
import { DEFAULT_CLIENT } from '../const/client';
import { useToast } from 'primevue/usetoast';
import { showToast } from '../../../composables/useToast';

function normalizeTags(tags) {
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

export function useClientForm(onSuccess) {
    const toast = useToast();
    const newClient = ref({ ...DEFAULT_CLIENT });
    const editingClient = ref(null);

    const errors = ref({});
    const submitting = ref(false);

    const isEditing = () => editingClient.value !== null;

    const loadClientForEdit = (client) => {
        if (client) {
            editingClient.value = client;
            newClient.value = {
                name: client.name || '',
                email: client.email || '',
                phone: client.phone || '',
                status: client.status || 'active',
                tags: normalizeTags(client.tags)
            };
        }
    };

    const resetForm = () => {
        newClient.value = { ...DEFAULT_CLIENT };
        errors.value = {};
        editingClient.value = null;
    };

    const validateForm = () => {
        errors.value = {};
        let isValid = true;

        if (!newClient.value.name || newClient.value.name.trim() === '') {
            errors.value.name = 'El nombre es requerido';
            isValid = false;
        }

        if (!newClient.value.email || newClient.value.email.trim() === '') {
            errors.value.email = 'El email es requerido';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newClient.value.email)) {
            errors.value.email = 'El email no es válido';
            isValid = false;
        }

        if (!newClient.value.phone || newClient.value.phone.trim() === '') {
            errors.value.phone = 'El teléfono es requerido';
            isValid = false;
        } else if (newClient.value.phone.length > 50) {
            errors.value.phone = 'El teléfono debe tener menos de 50 caracteres';
            isValid = false;
        }

        if (!newClient.value.status) {
            errors.value.status = 'El estado es requerido';
            isValid = false;
        }

        if (!Array.isArray(newClient.value.tags)) {
            errors.value.tags = 'Los tags deben ser un array';
            isValid = false;
        } else {
            const invalidTag = newClient.value.tags.find(t => typeof t !== 'string' || t.length > 50);
            if (invalidTag !== undefined) {
                errors.value.tags = 'Cada tag debe ser una cadena de máximo 50 caracteres';
                isValid = false;
            }
        }

        return isValid;
    };

    const createClient = async () => {
        if (!validateForm()) return;

        submitting.value = true;
        errors.value = {};

        try {
            const dataForm = {
                name: newClient.value.name,
                email: newClient.value.email,
                phone: newClient.value.phone,
                status: newClient.value.status,
                tags: newClient.value.tags
            };

            const response = await axios.post(route('clients.store'), dataForm);

            if (response.status === 200) {
                showToast(toast, {
                    severity: 'success',
                    summary: 'Cliente creado',
                    detail: response.data?.message
                });
                resetForm();
                onSuccess();
            }
        } catch (error) {
            showToast(toast, {
                severity: 'error',
                summary: 'Error',
                detail: error.response?.data?.message || 'Error al crear el cliente'
            });
            errors.value = error.response?.data?.errors || {};
            errors.value.general = error.response?.data?.message;
        } finally {
            submitting.value = false;
        }
    };

    const updateClient = async () => {
        if (!validateForm()) return;

        if (!editingClient.value) {
            showToast(toast, {
                severity: 'error',
                summary: 'Error',
                detail: 'No se ha seleccionado un cliente para editar'
            });
            return;
        }

        submitting.value = true;
        errors.value = {};

        try {
            const dataForm = {
                name: newClient.value.name,
                email: newClient.value.email,
                phone: newClient.value.phone,
                status: newClient.value.status,
                tags: newClient.value.tags
            };

            const response = await axios.put(
                route('clients.update', { id: editingClient.value.id }),
                dataForm
            );

            if (response.status === 200) {
                showToast(toast, {
                    severity: 'success',
                    summary: 'Cliente actualizado',
                    detail: response.data?.message
                });
                resetForm();
                onSuccess();
            }
        } catch (error) {
            showToast(toast, {
                severity: 'error',
                summary: 'Error',
                detail: error.response?.data?.message || 'Error al actualizar el cliente'
            });
            errors.value = error.response?.data?.errors || {};
            errors.value.general = error.response?.data?.message;
        } finally {
            submitting.value = false;
        }
    };

    return {
        newClient,
        editingClient,
        errors,
        submitting,
        isEditing,
        resetForm,
        loadClientForEdit,
        validateForm,
        createClient,
        updateClient,
    };
}

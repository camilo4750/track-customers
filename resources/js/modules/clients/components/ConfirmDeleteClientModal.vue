<script setup>
import Button from 'primevue/button';

const props = defineProps({
    client: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['confirm', 'cancel']);

const handleConfirm = () => {
    emit('confirm', props.client);
};

const handleCancel = () => {
    emit('cancel');
};
</script>

<template>
    <div class="p-4 space-y-4">
        <p class="text-sm text-gray-700 dark:text-gray-200">
            ¿Estás seguro de que deseas eliminar al cliente
            <span class="font-semibold">{{ client.name }}</span>?
            Esta acción no se puede deshacer.
        </p>

        <div class="flex justify-end gap-2 mt-4">
            <Button
                label="Cancelar"
                severity="secondary"
                outlined
                size="small"
                @click="handleCancel"
            />
            <Button
                label="Eliminar"
                severity="danger"
                size="small"
                @click="handleConfirm"
            />
        </div>
    </div>
</template>

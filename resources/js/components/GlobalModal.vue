<script setup>
import { computed } from 'vue';
import Dialog from 'primevue/dialog';
import { modalState } from '../composables/useModal';
import { useModal } from '../composables/useModal';

const { closeModal, handleConfirm, handleCancel } = useModal();

const modalSizeClasses = {
    sm: 'max-w-md',
    md: 'max-w-lg',
    lg: 'max-w-2xl',
    xl: 'max-w-4xl',
    full: 'max-w-full mx-4'
};

const modalClass = computed(() => {
    return modalSizeClasses[modalState.size] || modalSizeClasses.md;
});

const visible = computed({
    get: () => modalState.visible,
    set: (value) => {
        if (!value) {
            closeModal();
        }
    }
});
</script>

<template>
    <Dialog
        v-model:visible="visible"
        :modal="true"
        :closable="modalState.closable"
        :dismissableMask="modalState.dismissableMask"
        :style="{ width: '90vw' }"
        :class="modalClass"
        :header="modalState.title"
    >
        <template #header v-if="modalState.title">
            <div class="flex items-center justify-between w-full">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ modalState.title }}</h3>
            </div>
        </template>

        <component 
            v-if="modalState.component"
            :is="modalState.component"
            v-bind="modalState.props"
            @confirm="handleConfirm"
            @cancel="handleCancel"
        />
        
        <div v-else-if="modalState.visible && !modalState.component" class="p-4">
            <p class="text-gray-500">No content provided</p>
        </div>
    </Dialog>
</template>

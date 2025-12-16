<script setup>
    const props = defineProps({
        message: {
            type: String,
            default: 'Este es un ejemplo de contenido del modal'
        },
        data: {
            type: Object,
            default: () => ({})
        }
    });
    
    const emit = defineEmits(['confirm', 'cancel']);
    
    const handleConfirm = () => {
        emit('confirm', props.data);
    };
    
    const handleCancel = () => {
        emit('cancel');
    };
    </script>
    
    <template>
        <div class="p-4">
            <p class="mb-4 text-gray-700 dark:text-gray-300">{{ message }}</p>
            <div class="flex justify-end gap-2">
                <button 
                    @click="handleCancel"
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300 transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    @click="handleConfirm"
                    class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700 transition-colors"
                >
                    Confirmar
                </button>
            </div>
        </div>
    </template>
    
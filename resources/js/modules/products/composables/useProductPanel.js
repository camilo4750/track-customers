import { ref } from 'vue';

export function useProductPanel() {
    const showCreatePanel = ref(false);
    const showEditPanel = ref(false);
    const editingProduct = ref(null);

    const openCreatePanel = () => {
        showCreatePanel.value = true;
    };

    const closeCreatePanel = () => {
        showCreatePanel.value = false;
    };

    const openEditPanel = () => {
        showEditPanel.value = true;
    };

    const closeEditPanel = () => {
        showEditPanel.value = false;
        editingProduct.value = null;
    };

    return {
        showCreatePanel,
        showEditPanel,
        editingProduct,
        openCreatePanel,
        closeCreatePanel,
        openEditPanel,
        closeEditPanel,
    };
}

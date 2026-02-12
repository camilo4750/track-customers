import { ref } from 'vue';

export function useClientPanel() {
    const showCreatePanel = ref(false);
    const showEditPanel = ref(false);
    const editingClient = ref(null);

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
        editingClient.value = null;
    };

    return {
        showCreatePanel,
        showEditPanel,
        editingClient,
        openCreatePanel,
        closeCreatePanel,
        openEditPanel,
        closeEditPanel,
    };
}

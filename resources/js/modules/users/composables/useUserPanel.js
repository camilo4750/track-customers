import { ref } from 'vue';

export function useUserPanel() {
    const showCreatePanel = ref(false);
    const showEditPanel = ref(false);
    const editingUser = ref(null);

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
        editingUser.value = null;
    };


    return {
        showCreatePanel,
        showEditPanel,
        editingUser,
        openCreatePanel,
        closeCreatePanel,
        openEditPanel,
        closeEditPanel,
    };
}
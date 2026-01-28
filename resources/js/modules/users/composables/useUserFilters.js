import { ref } from 'vue';
import { DEFAULT_FILTERS } from '../const/user';

/**
 * Composable para manejar la lógica de filtros de usuarios
 * @returns {object} Estado y métodos para manejar filtros
 */
export function useUserFilters() {
    // Estado del panel de filtros
    const showFilterPanel = ref(false);

    // Estado de los filtros avanzados
    const advancedFilters = ref({ ...DEFAULT_FILTERS });

    /**
     * Abre el panel de filtros
     */
    const openFilterPanel = () => {
        showFilterPanel.value = true;
    };

    /**
     * Cierra el panel de filtros
     */
    const closeFilterPanel = () => {
        showFilterPanel.value = false;
    };

    /**
     * Limpia los filtros (método simple para uso directo)
     */
    const clearFilter = () => {
        advancedFilters.value = { ...DEFAULT_FILTERS };
    };

    /**
     * Aplica los filtros avanzados
     * @param {object} filters - Objeto con los filtros a aplicar
     */
    const applyAdvancedFilters = (filters) => {
        advancedFilters.value = { ...filters };
    };

    /**
     * Limpia los filtros avanzados
     */
    const clearAdvancedFilters = () => {
        advancedFilters.value = { ...DEFAULT_FILTERS };
    };

    return {
        showFilterPanel,
        advancedFilters,
        openFilterPanel,
        closeFilterPanel,
        clearFilter,
        applyAdvancedFilters,
        clearAdvancedFilters
    };
}

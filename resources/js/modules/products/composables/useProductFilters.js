import { ref } from 'vue';
import { DEFAULT_FILTERS } from '../const/product';

/**
 * Composable para manejar la lógica de filtros de productos
 * @returns {object} Estado y métodos para manejar filtros
 */
export function useProductFilters() {
    const showFilterPanel = ref(false);
    const advancedFilters = ref({ ...DEFAULT_FILTERS });

    const openFilterPanel = () => {
        showFilterPanel.value = true;
    };

    const closeFilterPanel = () => {
        showFilterPanel.value = false;
    };

    const clearFilter = () => {
        advancedFilters.value = { ...DEFAULT_FILTERS };
    };

    const applyAdvancedFilters = (filters) => {
        advancedFilters.value = { ...filters };
    };

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

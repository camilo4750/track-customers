/**
 * Formatea una fecha a formato legible en español
 * @param {string|null|undefined} dateString - String de fecha a formatear
 * @returns {string} Fecha formateada o '-' si no hay fecha
 */
export const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
};


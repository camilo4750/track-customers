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

/**
 * Formatea un número como precio en moneda (locale es-CL)
 * @param {number|null|undefined} value - Valor numérico
 * @returns {string} Precio formateado o '-' si no hay valor
 */
export const formatPrice = (value) => {
    if (value == null || value === '' || Number.isNaN(Number(value))) return '-';
    return new Intl.NumberFormat('es-CL', {
        style: 'currency',
        currency: 'CLP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(Number(value));
};


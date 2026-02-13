// Definición de todas las columnas disponibles para la tabla
export const COLUMNS = [
    { field: 'id', header: 'ID' },
    { field: 'name', header: 'Nombre' },
    { field: 'sku', header: 'SKU' },
    { field: 'price', header: 'Precio' },
    { field: 'category', header: 'Categoría' },
    { field: 'created_at', header: 'Creado' },
    { field: 'updated_at', header: 'Actualizado' }
];

// Columnas visibles por defecto
export const DEFAULT_COLUMNS = [
    { field: 'id', header: 'ID' },
    { field: 'name', header: 'Nombre' },
    { field: 'sku', header: 'SKU' },
    { field: 'price', header: 'Precio' },
    { field: 'category', header: 'Categoría' }
];

// Valores por defecto para un nuevo producto
export const DEFAULT_PRODUCT = {
    name: '',
    sku: '',
    price: null,
    category: ''
};

// Valores por defecto para filtros avanzados
export const DEFAULT_FILTERS = {
    category: null
};

// Configuración de filtros por columna para AdvancedDataTable
export const FILTERABLE_COLUMNS = [
    { field: 'id', type: 'number', operator: 'equals' },
    { field: 'name', type: 'text', matchMode: 'contains' },
    { field: 'sku', type: 'text', matchMode: 'contains' },
    { field: 'price', type: 'number', operator: 'equals' },
    { field: 'category', type: 'text', matchMode: 'contains' }
];

export const MODULE_LABELS = { Product: 'Producto' };
export const ACTION_LABELS = { show: 'Ver', create: 'Crear', edit: 'Editar', delete: 'Eliminar' };

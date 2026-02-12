// Estados disponibles
export const STATUSES = [
    { label: 'Activo', value: 'active' },
    { label: 'Inactivo', value: 'inactive' }
];

// Definición de todas las columnas disponibles para la tabla
export const COLUMNS = [
    { field: 'id', header: 'ID' },
    { field: 'name', header: 'Nombre' },
    { field: 'email', header: 'Email' },
    { field: 'phone', header: 'Teléfono' },
    { field: 'status', header: 'Estado' },
    { field: 'tags', header: 'Tags' },
    { field: 'created_at', header: 'Creado' },
    { field: 'updated_at', header: 'Actualizado' }
];

// Columnas visibles por defecto
export const DEFAULT_COLUMNS = [
    { field: 'id', header: 'ID' },
    { field: 'name', header: 'Nombre' },
    { field: 'email', header: 'Email' },
    { field: 'phone', header: 'Teléfono' },
    { field: 'status', header: 'Estado' }
];

// Valores por defecto para un nuevo cliente
export const DEFAULT_CLIENT = {
    name: '',
    email: '',
    phone: '',
    status: 'active',
    tags: []
};

// Valores por defecto para filtros avanzados
export const DEFAULT_FILTERS = {
    status: null
};

// Configuración de filtros por columna para AdvancedDataTable
export const FILTERABLE_COLUMNS = [
    { field: 'id', type: 'number', operator: 'equals' },
    { field: 'name', type: 'text', matchMode: 'contains' },
    { field: 'email', type: 'text', matchMode: 'contains' },
    { field: 'phone', type: 'text', matchMode: 'contains' },
    { field: 'status', type: 'text', matchMode: 'contains' }
];

export const MODULE_LABELS = { Client: 'Cliente' };
export const ACTION_LABELS = { show: 'Ver', create: 'Crear', edit: 'Editar', delete: 'Eliminar' };

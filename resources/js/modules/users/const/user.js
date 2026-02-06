// Roles disponibles
export const ROLES = [
    { label: 'Usuario', value: 'user' },
    { label: 'Administrador', value: 'admin' }
];

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
    { field: 'role', header: 'Rol' },
    { field: 'status', header: 'Estado' },
    { field: 'email_verified_at', header: 'Email Verificado' },
    { field: 'created_at', header: 'Creado' },
    { field: 'updated_at', header: 'Actualizado' }
];

// Columnas visibles por defecto
export const DEFAULT_COLUMNS = [
    { field: 'id', header: 'ID' },
    { field: 'name', header: 'Nombre' },
    { field: 'email', header: 'Email' },
    { field: 'role', header: 'Rol' },
    { field: 'status', header: 'Estado' }
];

// Valores por defecto para un nuevo usuario
export const DEFAULT_USER = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
    status: 'active'
};

// Valores por defecto para filtros avanzados
export const DEFAULT_FILTERS = {
    role: null,
    status: null,
};

// Configuración de filtros por columna para AdvancedDataTable
export const FILTERABLE_COLUMNS = [
    { field: 'id', type: 'number', operator: 'equals' },  // ID numérico
    { field: 'name', type: 'text', matchMode: 'contains' },  // Nombre texto
    { field: 'email', type: 'text', matchMode: 'contains' },  // Email texto
    { field: 'role', type: 'text', matchMode: 'contains' },  // Rol con dropdown personalizado
    { field: 'status', type: 'text', matchMode: 'contains' }  // Estado con dropdown personalizado
];


export const MODULE_LABELS = { User: 'Usuario'};
export const ACTION_LABELS = { show: 'Ver', create: 'Crear', edit: 'Editar', delete: 'Eliminar' };
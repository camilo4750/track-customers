import { useAuth } from '@/composables/useAuth';

/**
 * Guard para proteger rutas que requieren autenticación
 * 
 * Uso en componentes:
 * 
 * <script setup>
 * import { onMounted } from 'vue';
 * import { requireAuth } from '@/utils/authGuard';
 * 
 * onMounted(() => {
 *     requireAuth();
 * });
 * </script>
 * 
 * O en Inertia middleware:
 * 
 * router.on('before', (event) => {
 *     if (event.detail.visit.url.pathname !== '/login') {
 *         requireAuth();
 *     }
 * });
 */

/**
 * Verifica si el usuario está autenticado y redirige al login si no lo está
 * 
 * @param {string} redirectTo - URL a la que redirigir si no está autenticado login (default: '/')
 * @returns {boolean} true si está autenticado, false en caso contrario
 */
export function requireAuth(redirectTo = '/') {
    const { isAuthenticated, isTokenExpired } = useAuth();

    if (!isAuthenticated.value || isTokenExpired()) {
        const currentPath = window.location.pathname;
        const returnUrl = currentPath !== redirectTo ? `?returnUrl=${encodeURIComponent(currentPath)}` : '';
        
        // Redirigir al login
        window.location.href = `${redirectTo}${returnUrl}`;
        return false;
    }

    return true;
}

/**
 * Verifica si el usuario NO está autenticado (para rutas de guest como login/register)
 * 
 * @param {string} redirectTo - URL a la que redirigir si está autenticado (default: '/dashboard')
 * @returns {boolean} true si no está autenticado, false en caso contrario
 */
export function requireGuest(redirectTo = '/dashboard') {
    const { isAuthenticated, isTokenExpired } = useAuth();

    if (isAuthenticated.value && !isTokenExpired()) {
        window.location.href = redirectTo;
        return false;
    }

    return true;
}

/**
 * Verifica si el usuario tiene un rol específico
 * 
 * @param {string|string[]} roles - Rol o roles requeridos
 * @param {string} redirectTo - URL a la que redirigir si no tiene el rol (default: '/unauthorized')
 * @returns {boolean} true si tiene el rol, false en caso contrario
 */
export function requireRole(roles, redirectTo = '/unauthorized') {
    const { getUserFromToken } = useAuth();
    
    if (!requireAuth()) {
        return false;
    }

    const user = getUserFromToken();
    const userRole = user?.role || user?.roles || [];

    const requiredRoles = Array.isArray(roles) ? roles : [roles];
    
    const hasRole = requiredRoles.some(role => {
        if (Array.isArray(userRole)) {
            return userRole.includes(role);
        }
        return userRole === role;
    });

    if (!hasRole) {
        window.location.href = redirectTo;
        return false;
    }

    return true;
}

/**
 * Verifica si el usuario tiene un permiso específico
 * 
 * @param {string|string[]} permissions - Permiso o permisos requeridos
 * @param {string} redirectTo - URL a la que redirigir si no tiene el permiso (default: '/unauthorized')
 * @returns {boolean} true si tiene el permiso, false en caso contrario
 */
export function requirePermission(permissions, redirectTo = '/unauthorized') {
    const { getUserFromToken } = useAuth();
    
    if (!requireAuth()) {
        return false;
    }

    const user = getUserFromToken();
    const userPermissions = user?.permissions || [];

    // Convertir a array si es un string
    const requiredPermissions = Array.isArray(permissions) ? permissions : [permissions];
    
    // Verificar si el usuario tiene todos los permisos requeridos
    const hasPermissions = requiredPermissions.every(permission => 
        userPermissions.includes(permission)
    );

    if (!hasPermissions) {
        window.location.href = redirectTo;
        return false;
    }

    return true;
}

/**
 * Hook de Inertia para proteger rutas globalmente
 * 
 * Uso en app.js:
 * 
 * import { setupAuthGuard } from '@/utils/authGuard';
 * 
 * createInertiaApp({
 *     setup({ el, App, props, plugin }) {
 *         const app = createApp({ render: () => h(App, props) });
 *         
 *         // Configurar guard global
 *         setupAuthGuard(plugin.router, {
 *             publicRoutes: ['/login', '/register', '/forgot-password'],
 *             redirectTo: '/login'
 *         });
 *         
 *         return app.use(plugin).mount(el);
 *     }
 * });
 */
export function setupAuthGuard(router, options = {}) {
    const {
        publicRoutes = ['/login', '/register'],
        redirectTo = '/login'
    } = options;

    router.on('before', (event) => {
        const url = event.detail.visit.url.pathname;
        
        if (publicRoutes.includes(url)) {
            return;
        }

        requireAuth(redirectTo);
    });
}

import axios from 'axios';
import Cookies from 'js-cookie';

window.axios = axios;

// Headers comunes
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';

// Configurar interceptor para agregar token JWT automáticamente
axios.interceptors.request.use(
    (config) => {
        const token = Cookies.get('jwt_token');
        
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para manejar errores de autenticación
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        // Si el token expiró o es inválido (401 Unauthorized)
        if (error.response?.status === 401) {
            Cookies.remove('jwt_token', { path: '/' });
            
            // Redirigir al login si no estamos ya allí
            if (window.location.pathname !== '/') {
                window.location.href = '/';
            }
        }

        return Promise.reject(error);
    }
);

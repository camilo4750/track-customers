import { ref, computed } from 'vue';
import Cookies from 'js-cookie';
import axios from 'axios';

// Nombre de la cookie para el token JWT
const TOKEN_COOKIE_NAME = 'jwt_token';

// Estado reactivo global para el token
const token = ref(Cookies.get(TOKEN_COOKIE_NAME) || null);

/**
 * Composable para manejar la autenticación con JWT
 * 
 * @returns {Object} Objeto con estado y métodos de autenticación
 */
export function useAuth() {
    /**
     * Verifica si el usuario está autenticado
     */
    const isAuthenticated = computed(() => !!token.value);

    /**
     * Guarda el token JWT en una cookie y en el estado reactivo
     * 
     * @param {string} jwtToken - Token JWT recibido del backend
     * @param {number} expiresInMinutes - Tiempo de expiración en minutos (default: 60)
     */
    const setToken = (jwtToken, expiresInMinutes = 60) => {
        if (!jwtToken) {
            console.error('Token JWT no válido');
            return;
        }

        // Calcular fecha de expiración
        const expires = expiresInMinutes / (24 * 60); // Convertir minutos a días

        // Guardar en cookie
        Cookies.set(TOKEN_COOKIE_NAME, jwtToken, {
            expires, // Días
            secure: import.meta.env.PROD, // Solo HTTPS en producción
            sameSite: 'strict', // Protección CSRF
            path: '/' // Disponible en toda la app
        });

        // Actualizar estado reactivo
        token.value = jwtToken;

        // Configurar axios para usar el token
        configureAxiosInterceptor();
    };

    /**
     * Obtiene el token JWT actual
     * 
     * @returns {string|null} Token JWT o null si no existe
     */
    const getToken = () => {
        return token.value;
    };

    /**
     * Elimina el token JWT de la cookie y del estado
     */
    const removeToken = () => {
        Cookies.remove(TOKEN_COOKIE_NAME, { path: '/' });
        token.value = null;
    };

    /**
     * Decodifica el payload del token JWT (sin verificar firma)
     * NOTA: Esto es solo para leer datos, NO para validar el token
     * 
     * @returns {Object|null} Payload decodificado o null si hay error
     */
    const decodeToken = () => {
        if (!token.value) {
            return null;
        }

        try {
            // JWT tiene formato: header.payload.signature
            const parts = token.value.split('.');
            
            if (parts.length !== 3) {
                console.error('Token JWT inválido');
                return null;
            }

            // Decodificar el payload (segunda parte)
            const payload = parts[1];
            const decodedPayload = JSON.parse(atob(payload));

            return decodedPayload;
        } catch (error) {
            console.error('Error al decodificar token JWT:', error);
            return null;
        }
    };

    /**
     * Verifica si el token ha expirado
     * 
     * @returns {boolean} true si el token ha expirado, false en caso contrario
     */
    const isTokenExpired = () => {
        const payload = decodeToken();
        
        if (!payload || !payload.exp) {
            return true;
        }

        // exp está en segundos, Date.now() en milisegundos
        const currentTime = Math.floor(Date.now() / 1000);
        return payload.exp < currentTime;
    };

    /**
     * Obtiene información del usuario desde el token
     * 
     * @returns {Object|null} Datos del usuario o null si no hay token
     */
    const getUserFromToken = () => {
        const payload = decodeToken();
        
        if (!payload) {
            return null;
        }

        return {
            id: payload.sub || payload.user_id || null,
            email: payload.email || null,
            name: payload.name || null,
            ...payload // Incluir cualquier otro dato del payload
        };
    };

    /**
     * Configura axios para enviar el token en cada petición
     */
    const configureAxiosInterceptor = () => {
        // Interceptor para agregar el token a las peticiones
        axios.interceptors.request.use(
            (config) => {
                const currentToken = getToken();
                
                if (currentToken) {
                    // Agregar token en el header Authorization
                    config.headers.Authorization = `Bearer ${currentToken}`;
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
                // Si el token expiró o es inválido (401)
                if (error.response?.status === 401) {
                    removeToken();
                    
                    // Redirigir al login (puedes personalizar esta lógica)
                    if (window.location.pathname !== '/login') {
                        window.location.href = '/login';
                    }
                }

                return Promise.reject(error);
            }
        );
    };

    /**
     * Realiza login y guarda el token
     * 
     * @param {string} email - Email del usuario
     * @param {string} password - Contraseña del usuario
     * @returns {Promise<Object>} Respuesta del backend
     */
    const login = async (email, password) => {
        try {
            const response = await axios.post(route('auth.login'), {
                email,
                password
            });

            // El backend puede retornar token en response.data o response.data.data
            const data = response.data.data || response.data;
            const authToken = data.token;

            if (response.data.success && authToken) {
                const expiresIn = data.expires_in || 60; // minutos
                setToken(authToken, expiresIn);

                return response.data;
            }

            throw new Error('Token no recibido del servidor');
        } catch (error) {
            console.error('Error en login:', error);
            throw error;
        }
    };

    /**
     * Realiza logout y elimina el token
     * 
     * @returns {Promise<void>}
     */
    const logout = async () => {
        try {
            // Llamar al endpoint de logout si existe
            if (getToken()) {
                await axios.post(route('auth.logout'));
            }
        } catch (error) {
            console.error('Error en logout:', error);
        } finally {
            // Siempre eliminar el token localmente
            removeToken();
            
            // Redirigir al login
            window.location.href = '/';
        }
    };

    // Configurar interceptor al inicializar
    if (token.value) {
        configureAxiosInterceptor();
    }

    return {
        // Estado
        token,
        isAuthenticated,

        // Métodos de gestión de token
        setToken,
        getToken,
        removeToken,
        decodeToken,
        isTokenExpired,
        getUserFromToken,

        // Métodos de autenticación
        login,
        logout
    };
}

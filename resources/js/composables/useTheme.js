import { ref, watch } from 'vue';

const isDarkMode = ref(false);

// Aplicar el tema al documento
const applyTheme = () => {
    if (typeof document !== 'undefined') {
        if (isDarkMode.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
};

// Cargar el tema guardado del localStorage o usar el preferido del sistema
const loadTheme = () => {
    if (typeof window !== 'undefined' && typeof localStorage !== 'undefined') {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            isDarkMode.value = savedTheme === 'dark';
        } else {
            // Usar la preferencia del sistema
            isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        applyTheme();
    }
};

// Toggle del tema
const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    if (typeof localStorage !== 'undefined') {
        localStorage.setItem('theme', isDarkMode.value ? 'dark' : 'light');
    }
    applyTheme();
};

// Observar cambios en el tema
watch(isDarkMode, () => {
    applyTheme();
});

// Cargar el tema inmediatamente si estamos en el cliente
if (typeof window !== 'undefined') {
    loadTheme();
}

export function useTheme() {
    return {
        isDarkMode,
        toggleTheme,
        loadTheme
    };
}

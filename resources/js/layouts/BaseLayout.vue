<script setup>
    import { ref, computed, onMounted, onUnmounted } from 'vue';
    import { router } from '@inertiajs/vue3';
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
    import { faBars, faXmark, faMap } from '@fortawesome/free-solid-svg-icons';
    import SidebarHeader from './components/SidebarHeader.vue';
    import SidebarMenu from './components/SidebarMenu.vue';
    import SidebarFooter from './components/SidebarFooter.vue';
    import DarkModeToggle from './components/DarkModeToggle.vue';
    import GlobalModal from '../components/GlobalModal.vue';
    import { useTheme } from '../composables/useTheme';
    import { useAuth } from '../composables/useAuth';
    import { faHome, faStore, faList, faLocationDot, faUsers, faPercent } from '@fortawesome/free-solid-svg-icons';

    const isOpen = ref(true);
    const isMobileMenuOpen = ref(false);
    const isMobile = ref(false);
    const { loadTheme } = useTheme();
    const { logout, getUserFromToken } = useAuth();


    // Datos del usuario desde el token JWT
    const currentUser = computed(() => getUserFromToken());
    const userName = computed(() => currentUser.value?.name);
    const userRole = computed(() => currentUser.value?.roles[0]);
    

    const MOBILE_BREAKPOINT = 768;

    const checkMobile = () => {
        isMobile.value = window.innerWidth < MOBILE_BREAKPOINT;
        if (!isMobile.value) {
            isMobileMenuOpen.value = false;
        }
    };

    onMounted(() => {
        loadTheme();
        checkMobile();
        window.addEventListener('resize', checkMobile);
        router.on('finish', () => {
            if (isMobile.value) closeMobileMenu();
        });
    });

    onUnmounted(() => {
        window.removeEventListener('resize', checkMobile);
    });

    const openMobileMenu = () => {
        isMobileMenuOpen.value = true;
    };

    const closeMobileMenu = () => {
        isMobileMenuOpen.value = false;
    };

    const handleLogout = async () => {
        await logout();
    };

    const menu = computed(() => {
        const allItems = [
            { label: 'Dashboard', icon: faHome, route: 'dashboard' },
            { label: 'Usuarios', icon: faUsers, route: 'users.index', roles: ['admin'], permissions: ['User.show'] },
            { label: 'Marketplace', icon: faStore, route: '' },
            { label: 'Órdenes', icon: faList, route: '' },
            { label: 'Seguimiento', icon: faLocationDot, route: '' },
            { label: 'Clientes', icon: faUsers, route: '' },
            { label: 'Descuentos', icon: faPercent, route: '' }
        ];
        const userRoles = currentUser.value?.roles || [];
        const userPermissions = currentUser.value?.permissions || [];
        return allItems.filter(item =>
            !item.roles || item.roles.some(r => userRoles.includes(r)) &&
            !item.permissions || item.permissions.some(p => userPermissions.includes(p))
        );
    });
</script>

<template>
    <div class="flex w-full min-h-screen overflow-x-hidden bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        <!-- Sidebar desktop: visible solo md y mayor -->
        <aside
            :class="[
                'hidden md:flex bg-white dark:bg-gray-800 shadow-sm border-r border-gray-200 dark:border-gray-700 transition-all duration-300 flex-col relative',
                isOpen ? 'w-60' : 'w-20'
            ]"
        >
            <SidebarHeader :is-open="isOpen" @toggle="isOpen = !isOpen" />
            <SidebarMenu :items="menu" :is-open="isOpen" />
            <DarkModeToggle :is-open="isOpen" />
            <SidebarFooter :is-open="isOpen" :user-name="userName" :user-role="userRole" @logout="handleLogout" />
        </aside>

        <!-- Header móvil: visible solo por debajo de md -->
        <header
            class="md:hidden fixed top-0 left-0 right-0 z-30 flex items-center justify-between px-4 h-14 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm"
        >
            <div class="flex items-center gap-2">
                <FontAwesomeIcon :icon="faMap" class="text-xl text-blue-600 dark:text-blue-400" />
                <h1 class="text-lg font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 dark:from-blue-400 dark:via-indigo-400 dark:to-blue-500 bg-clip-text text-transparent tracking-tight">
                    CliMap
                </h1>
            </div>
            <button
                type="button"
                aria-label="Abrir menú"
                @click="openMobileMenu"
                class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
                <FontAwesomeIcon :icon="faBars" class="text-xl" />
            </button>
        </header>

        <!-- Overlay: capa translúcida que bloquea interacción -->
        <Transition name="overlay">
            <div
                v-if="isMobileMenuOpen"
                role="button"
                tabindex="0"
                aria-label="Cerrar menú"
                class="md:hidden fixed inset-0 z-40 bg-black/50 transition-opacity"
                @click="closeMobileMenu"
                @keydown.enter="closeMobileMenu"
                @keydown.space.prevent="closeMobileMenu"
            />
        </Transition>

        <!-- Drawer móvil: 80% ancho, por encima del contenido -->
        <Transition name="drawer">
            <aside
                v-if="isMobileMenuOpen"
                class="md:hidden fixed inset-y-0 left-0 z-50 w-[80%] flex flex-col bg-white dark:bg-gray-800 shadow-xl border-r border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <FontAwesomeIcon :icon="faMap" class="text-2xl text-blue-600 dark:text-blue-400" />
                        <h2 class="text-xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 dark:from-blue-400 dark:via-indigo-400 dark:to-blue-500 bg-clip-text text-transparent">
                            CliMap
                        </h2>
                    </div>
                    <button
                        type="button"
                        aria-label="Cerrar menú"
                        @click="closeMobileMenu"
                        class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        <FontAwesomeIcon :icon="faXmark" class="text-xl" />
                    </button>
                </div>
                <SidebarMenu :items="menu" :is-open="true" />
                <DarkModeToggle :is-open="true" />
                <SidebarFooter :is-open="true" :user-name="userName" :user-role="userRole" @logout="handleLogout" />
            </aside>
        </Transition>

        <!-- Contenido principal -->
        <main
            class="flex-1 p-6 bg-gray-100 dark:bg-gray-900 transition-colors duration-300 overflow-x-auto md:min-h-screen pt-20 md:pt-6"
        >
            <slot />
        </main>
        <GlobalModal />
    </div>
</template>

<style scoped>
.overlay-enter-active,
.overlay-leave-active {
    transition: opacity 0.2s ease;
}
.overlay-enter-from,
.overlay-leave-to {
    opacity: 0;
}

.drawer-enter-active,
.drawer-leave-active {
    transition: transform 0.25s ease-out;
}
.drawer-enter-from,
.drawer-leave-to {
    transform: translateX(-100%);
}
</style>
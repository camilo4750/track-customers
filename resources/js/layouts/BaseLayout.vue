<script setup>
    import { ref, onMounted } from 'vue';
    import SidebarHeader from './components/SidebarHeader.vue';
    import SidebarMenu from './components/SidebarMenu.vue';
    import SidebarFooter from './components/SidebarFooter.vue';
    import DarkModeToggle from './components/DarkModeToggle.vue';
    import GlobalModal from '../components/GlobalModal.vue';
    import { useTheme } from '../composables/useTheme';
    import { faHome, faStore, faList, faLocationDot, faUsers, faPercent } from '@fortawesome/free-solid-svg-icons';
    
    const isOpen = ref(true);
    const { loadTheme } = useTheme();
    
    onMounted(() => {
        loadTheme();
    });
    
    const handleLogout = () => {
      console.log('Logout clicked')
    }
    
    const menu = [
      { label: 'Dashboard', icon: faHome },
      { label: 'Marketplace', icon: faStore },
      { label: 'Orders', icon: faList },
      { label: 'Tracking', icon: faLocationDot },
      { label: 'Customers', icon: faUsers },
      { label: 'Discounts', icon: faPercent }
    ]
    </script>
    
    <template>
      <div class="flex w-full min-h-screen overflow-x-hidden bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        <aside :class="['bg-white dark:bg-gray-800 shadow-sm border-r border-gray-200 dark:border-gray-700 transition-all duration-300 flex flex-col relative', isOpen ? 'w-60' : 'w-20']">
          <SidebarHeader :is-open="isOpen" @toggle="isOpen = !isOpen" />
          <SidebarMenu :items="menu" :is-open="isOpen" />
          <DarkModeToggle :is-open="isOpen" />
          <SidebarFooter :is-open="isOpen" user-name="Harper Nelson" user-role="Admin" @logout="handleLogout" />
        </aside>
        <main class="flex-1 p-6 bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
          <slot />
        </main>
        <GlobalModal />
      </div>
    </template>
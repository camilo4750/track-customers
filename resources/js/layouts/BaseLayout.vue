<script setup>
    import { ref } from 'vue';
    import SidebarHeader from './components/SidebarHeader.vue';
    import SidebarMenu from './components/SidebarMenu.vue';
    import SidebarFooter from './components/SidebarFooter.vue';
    import { faHome, faStore, faList, faLocationDot, faUsers, faPercent } from '@fortawesome/free-solid-svg-icons';
    
    const isOpen = ref(true)
    
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
      <div class="flex w-full min-h-screen overflow-x-hidden bg-gray-100">
        <aside :class="['bg-white shadow-sm border-r border-gray-200 transition-all duration-300 flex flex-col relative', isOpen ? 'w-60' : 'w-20']">
          <SidebarHeader :is-open="isOpen" @toggle="isOpen = !isOpen" />
          <SidebarMenu :items="menu" :is-open="isOpen" />
          <SidebarFooter :is-open="isOpen" user-name="Harper Nelson" user-role="Admin" @logout="handleLogout" />
        </aside>
        <main class="flex-1 p-6">
          <slot />
        </main>
      </div>
    </template>
<script setup>
import { ref } from 'vue';
import Button from 'primevue/button';


const isOpen = ref(true)

const menu = [
    { label: 'Dashboard', icon: 'pi pi-home' },
    { label: 'Marketplace', icon: 'pi pi-shopping-bag' },
    { label: 'Orders', icon: 'pi pi-list' },
    { label: 'Tracking', icon: 'pi pi-map-marker' },
    { label: 'Customers', icon: 'pi pi-users' },
    { label: 'Discounts', icon: 'pi pi-percentage' }
]
</script>

<template>
    <div class="flex w-full min-h-screen overflow-x-hidden bg-gray-100">
        <!-- SIDEBAR -->
        <aside :class="[
            'bg-white shadow-sm border-r border-gray-200 transition-all duration-300 flex flex-col',
            isOpen ? 'w-60' : 'w-20'
        ]">
            <!-- TOP: Toggle button -->
            <div class="flex items-center justify-between px-4 py-4">
                <span v-if="isOpen" class="font-semibold text-gray-700 text-lg">
                    Flup Domi ashsjadggsda 
                </span>
                <Button icon="pi pi-bars" class="p-button-text p-0 text-gray-600 hover:text-gray-900"
                    @click="isOpen = !isOpen" />
            </div>

            <!-- MENU -->
            <nav class="flex-1 mt-4">
                <ul class="space-y-1">
                    <li v-for="item in menu" :key="item.label">
                        <button
                            class="flex cursor-pointer items-center w-full px-4 py-3 hover:bg-gray-100 transition text-gray-700 group">
                            <i :class="item.icon + ' text-xl'"></i>

                            <span v-if="isOpen" class="ml-3 text-sm font-medium">
                                {{ item.label }}
                            </span>
                        </button>
                    </li>
                </ul>
            </nav>

            <!-- FOOTER: Perfil -->
            <div class="px-4 py-4 border-t border-gray-200 mt-auto">
                <div class="flex items-center" :class="{ 'gap-3': isOpen }">
                    <div class="w-8 h-8 rounded-full bg-gray-300"></div>

                    <div v-if="isOpen" class="flex flex-col">
                        <span class="text-sm font-medium text-gray-800">Harper Nelson</span>
                        <span class="text-xs text-gray-500">Admin</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- CONTENT AREA -->
        <main class="flex-1 p-6">
            <slot />
        </main>
    </div>
</template>
<script setup>
import { useTheme } from '../../composables/useTheme';
import ToggleSwitch from 'primevue/toggleswitch';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faMoon, faSun } from '@fortawesome/free-solid-svg-icons';

const { isDarkMode, toggleTheme } = useTheme();

defineProps({
    isOpen: Boolean
});
</script>

<template>
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center" :class="isOpen ? 'justify-between gap-3' : 'justify-center'">
            <div class="flex items-center gap-3" :class="isOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                <FontAwesomeIcon 
                    :icon="isDarkMode ? faMoon : faSun" 
                    class="text-lg transition-colors"
                    :class="isDarkMode ? 'text-blue-400' : 'text-yellow-500'"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                    {{ isDarkMode ? 'Dark Mode' : 'Light Mode' }}
                </span>
            </div>
            <div class="flex-shrink-0">
                <ToggleSwitch
                    :modelValue="isDarkMode"
                    @update:modelValue="toggleTheme"
                    :class="isOpen ? '' : 'scale-90'"
                />
            </div>
        </div>
    </div>
</template>

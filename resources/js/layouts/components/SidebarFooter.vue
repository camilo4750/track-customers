<script setup>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
    import { faRightFromBracket } from '@fortawesome/free-solid-svg-icons';
    
    defineProps({
      isOpen: Boolean,
      userName: String,
      userRole: String
    })
    
    defineEmits(['logout'])
    </script>
    
    <template>
      <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 mt-auto">
        <div class="flex items-center" :class="isOpen ? 'justify-between gap-3' : 'justify-center'">
          <div class="flex items-center" :class="isOpen ? 'gap-3' : 'gap-0'">
            <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex-shrink-0"></div>
            <div class="flex flex-col overflow-hidden transition-all duration-300" :class="isOpen ? 'opacity-100 max-w-[200px]' : 'opacity-0 max-w-0'">
              <span class="text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ userName }}</span>
              <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ userRole }}</span>
            </div>
          </div>
          <button v-if="isOpen" @click="$emit('logout')" class="p-2 text-red-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200 cursor-pointer bg-transparent border-none outline-none focus:outline-none" title="Cerrar sesión">
            <FontAwesomeIcon :icon="faRightFromBracket" class="text-lg" />
          </button>
        </div>
      </div>
    </template>
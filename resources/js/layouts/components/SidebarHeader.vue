<script setup>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
    import { faMap, faCircleChevronLeft, faCircleChevronRight } from '@fortawesome/free-solid-svg-icons';
    
    defineProps({
      isOpen: Boolean
    })
    
    defineEmits(['toggle'])
    </script>
    
    <template>
      <div class="relative flex items-center px-4 py-4" :class="isOpen ? 'justify-start' : 'justify-center'">
        <!-- Logo y título -->
        <div class="flex items-center" :class="isOpen ? 'gap-2' : 'justify-center'">
          <FontAwesomeIcon :icon="faMap" class="text-2xl text-blue-600 dark:text-blue-400" />
          <h1 v-if="isOpen" class="text-2xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 dark:from-blue-400 dark:via-indigo-400 dark:to-blue-500 bg-clip-text text-transparent tracking-tight">
            CliMap
          </h1>
        </div>
        
        <!-- Botón toggle flotante -->
        <button @click="$emit('toggle')" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/2 z-10 w-8 h-8 flex items-center justify-center bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 rounded-full shadow-md hover:shadow-lg hover:bg-slate-50 dark:hover:bg-gray-600 hover:border-slate-400 dark:hover:border-gray-500 text-slate-700 dark:text-gray-300 hover:text-slate-900 dark:hover:text-gray-100 transition-all duration-200 cursor-pointer outline-none focus:outline-none">
          <FontAwesomeIcon :icon="isOpen ? faCircleChevronLeft : faCircleChevronRight" class="text-md" />
        </button>
      </div>
    </template>
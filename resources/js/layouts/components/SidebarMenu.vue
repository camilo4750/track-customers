<script setup>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
    
    defineProps({
      items: Array,
      isOpen: Boolean
    })
    </script>
    
    <template>
      <nav class="flex-1 mt-4 overflow-y-auto overflow-x-hidden">
        <ul class="space-y-1 px-2">
          <li v-for="item in items" :key="item.label">
            <button class="relative flex cursor-pointer items-center w-full px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 group transition-all duration-200 ease-in-out hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:shadow-sm hover:scale-[1.02] hover:text-blue-600 dark:hover:text-blue-400 active:scale-[0.98]">
              <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 bg-gradient-to-b from-blue-500 to-indigo-500 rounded-r-full transition-all duration-200 ease-in-out group-hover:h-8 opacity-0 group-hover:opacity-100"></span>
              <FontAwesomeIcon :icon="item.icon" class="text-xl transition-all duration-200 group-hover:scale-110 group-hover:text-blue-600 dark:group-hover:text-blue-400" />
              <span v-if="isOpen" class="ml-3 text-sm font-medium transition-all duration-200 group-hover:translate-x-1">
                {{ item.label }}
              </span>
            </button>
          </li>
        </ul>
      </nav>
    </template>
<script setup>
import { defineProps } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faArrowTrendUp, faArrowTrendDown } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    metric: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-2 mb-4">
            <FontAwesomeIcon :icon="props.metric.icon" class="w-5 h-5 text-blue-500 dark:text-blue-400" />
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ props.metric.label }}</span>
        </div>
        <div class="mb-2">
            <span class="text-3xl font-bold text-gray-900 dark:text-blue-400">{{ props.metric.value }}</span>
        </div>
        <div class="flex items-center gap-1">
            <FontAwesomeIcon 
                :icon="props.metric.trend === 'up' ? faArrowTrendUp : faArrowTrendDown" 
                :class="[
                    'w-4 h-4',
                    props.metric.trend === 'up' ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'
                ]"
            />
            <span 
                :class="[
                    'text-sm font-medium',
                    props.metric.trend === 'up' ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'
                ]"
            >
                {{ props.metric.change }} %
            </span>
        </div>
    </div>
</template>
<script setup>
    import { ref } from 'vue';
    import BaseLayout from '../../../layouts/BaseLayout.vue';
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
    import { 
        faUsers, 
        faDollarSign, 
        faShoppingBag, 
        faBox, 
        faCalendar,
    } from '@fortawesome/free-solid-svg-icons';
    import { useModal } from '../../../composables/useModal';
    import DashboardFilters from '../components/DashboardFilters.vue';
    import KpiCard from '../components/KpiCard.vue';

    let openModal;
    const modal = useModal();
    openModal = modal.openModal;

    const dateRange = ref(null);
    const selectedClient = ref(null);
    
    const clients = ref([
        { id: 1, name: 'Cliente 1', email: 'cliente1@example.com' },
        { id: 2, name: 'Cliente 2', email: 'cliente2@example.com' },
        { id: 3, name: 'Cliente 3', email: 'cliente3@example.com' },
        { id: 4, name: 'Cliente 4', email: 'cliente4@example.com' },
        { id: 5, name: 'Cliente 5', email: 'cliente5@example.com' }
    ]);

    const metrics = [
        {
            label: 'Total customers',
            value: '567,899',
            change: '+2,5',
            trend: 'up',
            icon: faUsers
        },
        {
            label: 'Total revenue',
            value: '$3,465 M',
            change: '+0,5',
            trend: 'up',
            icon: faDollarSign
        },
        {
            label: 'Total orders',
            value: '1,136 M',
            change: '-0,2',
            trend: 'down',
            icon: faShoppingBag
        },
        {
            label: 'Total returns',
            value: '1,789',
            change: '+0,12',
            trend: 'up',
            icon: faBox
        }
    ];

    const handleAddData = async () => {
        const ModalKpi = (await import('../components/ModalKpi.vue')).default;
        openModal({
            title: 'Agregar Datos',
            component: ModalKpi,
            props: {
                message: 'Agregar Datos',
                data: {
                    source: 'Dashboard',
                    action: 'add_data'
                }
            },
            size: 'lg',
            closable: true,
            dismissableMask: true,
            onClose: () => {
                console.log('Modal cerrado');
            },
            onConfirm: (data) => {
            console.log('Confirmado con datos:', data);
                alert(`Datos confirmados: ${JSON.stringify(data)}`);
            },
            onCancel: () => {
                console.log('Operación cancelada');
            }
        });
    };
    const handleSearchFilters = (filters) => {
        console.log('Filtros aplicados:', filters);
    };
</script>

<template>
    <BaseLayout>
        <div class="w-full">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 bg-clip-text text-transparent dark:from-blue-400 dark:via-indigo-400 dark:to-blue-500">Dashboard</h1>
                <button class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <FontAwesomeIcon :icon="faCalendar" class="w-4 h-4" />
                    <span>Time period:</span>
                </button>
            </div>

            <DashboardFilters :clients="clients" @search="handleSearchFilters" />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <KpiCard v-for="metric in metrics" :key="metric.label" :metric="metric" />
            </div>
        </div>
    </BaseLayout>
</template>
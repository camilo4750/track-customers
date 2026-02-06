<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import BaseLayout from '../../../layouts/BaseLayout.vue';
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import ProgressSpinner from 'primevue/progressspinner';
import { MODULE_LABELS, ACTION_LABELS } from '../const/user';
import { faUser } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { requireRole, requirePermission} from '@/utils/authGuard';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
  id: {
    type: Number,
    required: true,
  },
});


const loading = ref(true);
const permissionsByModule = ref({});
const selectedPermissionIds = ref([]);
const userId = ref(props.id);
const userName = ref('');
const toast = useToast();

const MODULE_ICONS = {
  User: faUser,
};

// Estado: array de IDs seleccionados (patrón Group de PrimeVue Checkbox)
// Array.from evita Proxy(Array) de Inertia que puede fallar con v-model del Checkbox
const getPermissions = async () => {
  try {
    loading.value = true;
    const { data } = await axios.get(
      route('users.permissions.api', { id: userId.value })
    );

    if (data.success) {
      permissionsByModule.value = data.data.permissionsByModule ?? {};
      selectedPermissionIds.value = Array.from(
        data.data.userPermissionIds ?? data.data.selectedPermissionIds ?? []
      );
    } else {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: data.message || 'Error al obtener los permisos',
      });
    }
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
};

const save = async () => {
  loading.value = true;
  try {
    const { data } = await axios.put(
      route('users.permissions.sync', { id: userId.value }),
      { permission_ids: selectedPermissionIds.value }
    );
    if (data.success) {
      toast.add({
        severity: 'success',
        summary: 'Éxito',
        detail: data.message,
      });
    } else {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: data.message || 'Error al sincronizar los permisos',
      });
    }
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  if (requireRole('admin', '/dashboard') && requirePermission('User.show')) {
    await getPermissions();
  }
});
</script>
<template>
   <BaseLayout>
    <div class="w-full">
        <div v-if="loading" class="flex flex-col items-center justify-center min-h-[280px] gap-4" aria-busy="true">
            <ProgressSpinner />
            <span class="text-surface-500 dark:text-surface-400">Cargando permisos...</span>
        </div>

        <template v-else>
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-4 gap-3">
                <h1 class="text-xl font-semibold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400">
                    Gestión Permisos de Usuario
                </h1>
            </div>
            <Accordion>
                <AccordionPanel v-for="(permissions, moduleName) in permissionsByModule" :key="moduleName">
                    <AccordionHeader>
                        <div class="flex items-center">
                            <FontAwesomeIcon :icon="MODULE_ICONS[moduleName]" class="mr-2" />
                            {{ MODULE_LABELS[moduleName] }}
                        </div>
                    </AccordionHeader>
                    <AccordionContent>
                        <div class="flex flex-col gap-2">
                            <div v-for="perm in permissions" :key="perm.id" class="flex items-center gap-2">
                                <Checkbox
                                    v-model="selectedPermissionIds"
                                    :value="perm.id"
                                    :inputId="'perm-' + perm.id"
                                />
                                <label :for="'perm-' + perm.id">{{ ACTION_LABELS[perm.action] }}</label>
                            </div>
                        </div>
                    </AccordionContent>
                </AccordionPanel>
            </Accordion>
            <Button label="Guardar permisos" @click="save" class="mt-4 float-right" />
        </template>
    </div>
   </BaseLayout>
</template>

<style scoped>

</style>
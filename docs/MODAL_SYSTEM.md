# Sistema de Modales Global

Sistema de modales global basado en PrimeVue 4 que permite abrir modales desde cualquier componente de la aplicación de forma sencilla y consistente.

## 📋 Tabla de Contenidos

- [Descripción General](#descripción-general)
- [Arquitectura](#arquitectura)
- [Uso Básico](#uso-básico)
- [Opciones Disponibles](#opciones-disponibles)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Crear Componentes de Contenido](#crear-componentes-de-contenido)
- [Manejo de Eventos](#manejo-de-eventos)
- [Tamaños de Modal](#tamaños-de-modal)
- [Mejores Prácticas](#mejores-prácticas)

## 📖 Descripción General

El sistema de modales global proporciona una forma centralizada de mostrar diálogos modales en toda la aplicación. Utiliza PrimeVue Dialog como base y proporciona una API simple y reactiva para gestionar el estado de los modales.

### Características

- ✅ **Global**: Disponible en toda la aplicación sin necesidad de importar en cada componente
- ✅ **Reactivo**: Estado compartido usando Vue 3 Composition API
- ✅ **Flexible**: Soporta cualquier componente de Vue como contenido
- ✅ **Configurable**: Múltiples opciones de personalización (tamaño, cierre, callbacks)
- ✅ **Type-safe**: Compatible con TypeScript (si se usa)
- ✅ **Eventos**: Soporte para eventos `confirm` y `cancel`

## 🏗️ Arquitectura

El sistema está compuesto por tres partes principales:

1. **`useModal.js`** - Composable que gestiona el estado global del modal
2. **`GlobalModal.vue`** - Componente que renderiza el modal usando PrimeVue Dialog
3. **`BaseLayout.vue`** - Layout que incluye el `GlobalModal` para que esté disponible globalmente

```
┌─────────────────┐
│  Componente     │
│  (HomePage.vue) │
└────────┬────────┘
         │
         │ useModal()
         ▼
┌─────────────────┐
│  useModal.js    │
│  (Estado Global)│
└────────┬────────┘
         │
         │ modalState
         ▼
┌─────────────────┐
│ GlobalModal.vue │
│  (PrimeVue)     │
└─────────────────┘
```

## 🚀 Uso Básico

### 1. Importar el composable

```javascript
import { useModal } from '../../../composables/useModal';
```

### 2. Obtener las funciones del modal

```javascript
const { openModal, closeModal } = useModal();
```

### 3. Abrir un modal

```javascript
openModal({
    title: 'Mi Modal',
    component: MiComponente,
    props: {
        // Props que se pasarán al componente
    }
});
```

## ⚙️ Opciones Disponibles

### `openModal(options)`

| Opción | Tipo | Default | Descripción |
|--------|------|---------|-------------|
| `title` | `String` | `''` | Título del modal (se muestra en el header) |
| `component` | `Component` | `null` | Componente de Vue que se renderizará como contenido |
| `props` | `Object` | `{}` | Props que se pasarán al componente |
| `size` | `String` | `'md'` | Tamaño del modal: `'sm'`, `'md'`, `'lg'`, `'xl'`, `'full'` |
| `closable` | `Boolean` | `true` | Si se muestra el botón de cerrar en el header |
| `dismissableMask` | `Boolean` | `true` | Si se puede cerrar haciendo clic fuera del modal |
| `onClose` | `Function` | `null` | Callback que se ejecuta cuando se cierra el modal |
| `onConfirm` | `Function` | `null` | Callback que se ejecuta cuando se emite el evento `confirm` |
| `onCancel` | `Function` | `null` | Callback que se ejecuta cuando se emite el evento `cancel` |

### Funciones Disponibles

| Función | Descripción |
|---------|-------------|
| `openModal(options)` | Abre un modal con las opciones especificadas |
| `closeModal()` | Cierra el modal actual |
| `updateModalProps(props)` | Actualiza las props del componente del modal |
| `handleConfirm(data)` | Maneja el evento confirm (usado internamente) |
| `handleCancel()` | Maneja el evento cancel (usado internamente) |

## 📝 Ejemplos de Uso

### Ejemplo 1: Modal Simple

```javascript
<script setup>
import { useModal } from '../../../composables/useModal';
import MiComponente from './MiComponente.vue';

const { openModal } = useModal();

const abrirModal = () => {
    openModal({
        title: 'Título del Modal',
        component: MiComponente,
        props: {
            mensaje: 'Hola desde el modal'
        }
    });
};
</script>

<template>
    <button @click="abrirModal">Abrir Modal</button>
</template>
```

### Ejemplo 2: Modal con Importación Dinámica

```javascript
<script setup>
import { useModal } from '../../../composables/useModal';

const { openModal } = useModal();

const abrirModal = async () => {
    // Importación dinámica del componente
    const MiComponente = (await import('./MiComponente.vue')).default;
    
    openModal({
        title: 'Modal con Carga Dinámica',
        component: MiComponente,
        props: {
            datos: { id: 1, nombre: 'Ejemplo' }
        }
    });
};
</script>
```

### Ejemplo 3: Modal con Callbacks

```javascript
<script setup>
import { useModal } from '../../../composables/useModal';
import FormComponent from './FormComponent.vue';

const { openModal } = useModal();

const abrirFormulario = () => {
    openModal({
        title: 'Crear Usuario',
        component: FormComponent,
        props: {
            mode: 'create'
        },
        size: 'lg',
        closable: true,
        dismissableMask: false, // No permitir cerrar haciendo clic fuera
        onClose: () => {
            console.log('Modal cerrado');
        },
        onConfirm: (data) => {
            console.log('Datos confirmados:', data);
            // Aquí puedes hacer una petición al backend
            // await crearUsuario(data);
        },
        onCancel: () => {
            console.log('Operación cancelada');
        }
    });
};
</script>
```

### Ejemplo 4: Modal de Confirmación

```javascript
<script setup>
import { useModal } from '../../../composables/useModal';
import ConfirmDialog from './ConfirmDialog.vue';

const { openModal } = useModal();

const confirmarEliminacion = () => {
    openModal({
        title: 'Confirmar Eliminación',
        component: ConfirmDialog,
        props: {
            message: '¿Estás seguro de que deseas eliminar este elemento?',
            itemName: 'Usuario #123'
        },
        size: 'sm',
        dismissableMask: false,
        onConfirm: () => {
            // Lógica de eliminación
            console.log('Elemento eliminado');
        },
        onCancel: () => {
            console.log('Eliminación cancelada');
        }
    });
};
</script>
```

## 🎨 Crear Componentes de Contenido

Los componentes de contenido para modales son componentes Vue normales que pueden emitir eventos `confirm` y `cancel`.

### Estructura Básica

```vue
<script setup>
// Definir props que recibirá el componente
const props = defineProps({
    message: {
        type: String,
        default: 'Mensaje por defecto'
    },
    data: {
        type: Object,
        default: () => ({})
    }
});

// Definir eventos que el componente puede emitir
const emit = defineEmits(['confirm', 'cancel']);

// Función para confirmar
const handleConfirm = () => {
    // Puedes pasar datos al callback onConfirm
    emit('confirm', props.data);
};

// Función para cancelar
const handleCancel = () => {
    emit('cancel');
};
</script>

<template>
    <div class="p-4">
        <!-- Contenido del modal -->
        <p>{{ message }}</p>
        
        <!-- Botones de acción -->
        <div class="flex justify-end gap-2 mt-4">
            <button @click="handleCancel">Cancelar</button>
            <button @click="handleConfirm">Confirmar</button>
        </div>
    </div>
</template>
```

### Ejemplo Completo: Formulario en Modal

```vue
<script setup>
import { ref } from 'vue';

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['confirm', 'cancel']);

const formData = ref({
    name: props.initialData.name || '',
    email: props.initialData.email || ''
});

const handleSubmit = () => {
    // Validación
    if (!formData.value.name || !formData.value.email) {
        alert('Por favor completa todos los campos');
        return;
    }
    
    // Emitir confirm con los datos del formulario
    emit('confirm', formData.value);
};

const handleCancel = () => {
    emit('cancel');
};
</script>

<template>
    <div class="p-6">
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Nombre</label>
                <input 
                    v-model="formData.name"
                    type="text"
                    class="w-full px-3 py-2 border rounded"
                    required
                />
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Email</label>
                <input 
                    v-model="formData.email"
                    type="email"
                    class="w-full px-3 py-2 border rounded"
                    required
                />
            </div>
            
            <div class="flex justify-end gap-2 mt-6">
                <button 
                    type="button"
                    @click="handleCancel"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                >
                    Cancelar
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Guardar
                </button>
            </div>
        </form>
    </div>
</template>
```

## 🎯 Manejo de Eventos

El sistema de modales soporta dos eventos principales:

### Evento `confirm`

Se emite cuando el usuario confirma una acción (por ejemplo, hace clic en "Guardar" o "Confirmar").

```javascript
// En el componente de contenido
emit('confirm', datos);

// En el componente que abre el modal
openModal({
    // ...
    onConfirm: (data) => {
        // data contiene los datos pasados en emit('confirm', data)
        console.log('Datos recibidos:', data);
    }
});
```

### Evento `cancel`

Se emite cuando el usuario cancela una acción (por ejemplo, hace clic en "Cancelar").

```javascript
// En el componente de contenido
emit('cancel');

// En el componente que abre el modal
openModal({
    // ...
    onCancel: () => {
        console.log('Operación cancelada');
    }
});
```

**Nota**: Ambos eventos (`confirm` y `cancel`) cierran automáticamente el modal después de ejecutar sus callbacks.

## 📏 Tamaños de Modal

El sistema soporta los siguientes tamaños predefinidos:

| Tamaño | Clase CSS | Ancho Aproximado | Uso Recomendado |
|--------|-----------|------------------|-----------------|
| `sm` | `max-w-md` | ~448px | Confirmaciones, mensajes cortos |
| `md` | `max-w-lg` | ~512px | Formularios pequeños, contenido medio |
| `lg` | `max-w-2xl` | ~672px | Formularios medianos, tablas pequeñas |
| `xl` | `max-w-4xl` | ~896px | Formularios grandes, contenido extenso |
| `full` | `max-w-full mx-4` | Ancho completo | Contenido muy extenso, dashboards |

### Ejemplo de Uso de Tamaños

```javascript
// Modal pequeño para confirmación
openModal({
    title: 'Confirmar',
    component: ConfirmDialog,
    size: 'sm'
});

// Modal grande para formulario
openModal({
    title: 'Crear Usuario',
    component: UserForm,
    size: 'lg'
});
```

## 💡 Mejores Prácticas

### 1. Importación Dinámica para Mejor Performance

Usa importación dinámica para componentes de modal que no se usan frecuentemente:

```javascript
const abrirModal = async () => {
    const Componente = (await import('./Componente.vue')).default;
    openModal({ component: Componente });
};
```

### 2. Validación en Componentes de Contenido

Siempre valida los datos antes de emitir `confirm`:

```javascript
const handleConfirm = () => {
    if (!validarDatos()) {
        return; // No emitir si hay errores
    }
    emit('confirm', datos);
};
```

### 3. Manejo de Errores

Incluye manejo de errores en los callbacks:

```javascript
openModal({
    // ...
    onConfirm: async (data) => {
        try {
            await guardarDatos(data);
        } catch (error) {
            console.error('Error al guardar:', error);
            // Mostrar mensaje de error al usuario
        }
    }
});
```

### 4. Props Reactivas

Si necesitas actualizar las props del modal dinámicamente:

```javascript
const { updateModalProps } = useModal();

// Actualizar props sin cerrar el modal
updateModalProps({
    nuevoValor: 'actualizado'
});
```

### 5. Limpieza de Recursos

Si el componente del modal tiene suscripciones o timers, límpialos en `onUnmounted`:

```vue
<script setup>
import { onUnmounted } from 'vue';

// Suscripción o timer
const subscription = subscribe();

onUnmounted(() => {
    subscription.unsubscribe();
});
</script>
```

## 🔍 Ejemplo Real: HomePage.vue

Aquí tienes un ejemplo real de cómo se usa en `HomePage.vue`:

```javascript
<script setup>
import { useModal } from '../../../composables/useModal';

const { openModal } = useModal();

const handleAddData = async () => {
    const ExampleModalContent = (await import('../../../components/ExampleModalContent.vue')).default;
    
    openModal({
        title: 'Agregar Datos',
        component: ExampleModalContent,
        props: {
            message: 'Este es un ejemplo de cómo usar el modal global.',
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
            // Lógica de confirmación
        },
        onCancel: () => {
            console.log('Operación cancelada');
        }
    });
};
</script>
```

## 📚 Referencias

- [PrimeVue Dialog Documentation](https://primevue.org/dialog/)
- [Vue 3 Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)
- [Vue 3 Component Events](https://vuejs.org/guide/components/events.html)

## 🐛 Troubleshooting

### El modal no se muestra

- Verifica que `GlobalModal` esté incluido en `BaseLayout.vue`
- Asegúrate de que el componente pasado a `openModal` sea válido
- Revisa la consola del navegador para errores

### Los eventos no funcionan

- Asegúrate de que el componente emita los eventos correctamente: `emit('confirm', data)`
- Verifica que los callbacks `onConfirm` y `onCancel` estén definidos

### El modal no se cierra

- Verifica que `dismissableMask` esté en `true` si quieres cerrar haciendo clic fuera
- Asegúrate de que `closable` esté en `true` si quieres mostrar el botón de cerrar

---

**Última actualización**: Diciembre 2024  
**Versión**: 1.0.0



import { reactive, shallowRef } from 'vue'

const modalState = reactive({
    visible: false,
    title: '',
    component: shallowRef(null),
    props: {},
    size: 'md', // sm, md, lg, xl
    closable: true,
    dismissableMask: true,
    onClose: null,
    onConfirm: null,
    onCancel: null,
});

export function useModal() {
    const openModal = (options = {}) => {
        modalState.visible = true;
        modalState.title = options.title || '';
        modalState.component = options.component || null;
        modalState.props = options.props || {};
        modalState.size = options.size || 'md';
        modalState.closable = options.closable !== undefined ? options.closable : true;
        modalState.dismissableMask = options.dismissableMask !== undefined ? options.dismissableMask : true;
        modalState.onClose = options.onClose || null;
        modalState.onConfirm = options.onConfirm || null;
        modalState.onCancel = options.onCancel || null;
    };

    const closeModal = () => {
        if (modalState.onClose) {
            modalState.onClose();
        }
        modalState.visible = false;
        modalState.title = '';
        modalState.component = null;
        modalState.props = {};
        modalState.onClose = null;
    };

    const handleConfirm = (data) => {
        if (modalState.onConfirm) {
            modalState.onConfirm(data);
        }
        closeModal();
    };

    const handleCancel = () => {
        if (modalState.onCancel) {
            modalState.onCancel();
        }
        closeModal();
    };

    const updateModalProps = (props) => {
        modalState.props = { ...modalState.props, ...props };
    };

    return {
        modalState,
        openModal,
        closeModal,
        handleConfirm, 
        handleCancel,
        updateModalProps,
    };
}

// Exportar el estado directamente para uso en componentes
export { modalState };

/**
 * show a toast
 * @param data Object => {
 *     severity: 'info' | 'success' | 'warn' | 'error' | 'secondary' | 'contrast', default: 'success',
 *     summary: string,
 *     detail: string,
 *     life: milliseconds, default: 3000,
 *     position: 'top' | 'bottom' | 'left' | 'right' combine it, example: 'top-left', default: 'top-right'
 * }
 */
export const showToast = (toast, data) => {
    toast.add({
        severity: data.severity ?? 'success',
        summary: data.summary,
        detail: data.detail,
        life: data.life ?? 3000,
        position: data.position ?? 'top-right'
    })
}

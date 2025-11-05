import { ref } from "vue";

export function usePollingInterval(callback, interval, { onStart, onStop } = {}) {

    const timerId = ref(null);
    const start = () => {
        if (timerId.value === null) {
            if (onStart) {
                onStart();
                return;
            }

            timerId.value = setInterval(callback, interval);
        }
    }

    const stop = () => {
        if (timerId.value !== null) {
            if (onStop) {
                onStop();
                return;
            }

            clearInterval(timerId.value);
            timerId.value = null;
        }
    }

    return {
        start,
        stop,
        timerId,
    };
}

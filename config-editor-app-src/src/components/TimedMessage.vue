<script setup>
import { computed, onMounted, onUnmounted } from "vue";
import { useDmfStore } from '../stores/dmf';
import { useNotifications } from "../composables/notifications";

const store = useDmfStore();
const { removeMessage } = useNotifications(store);

const props = defineProps({
    index: {
        type: Number,
        required: true
    }
});

let messageTimeout = null;

const remove = () => {
    removeMessage(props.index);
    stopTimer();
};
const stopTimer = () => {
    if (messageTimeout !== null) {
        clearTimeout(messageTimeout);
        messageTimeout = null;
    }
};
const startTimer = () => {
    stopTimer();
    messageTimeout = setTimeout(() => {
        messageTimeout = null;
        remove();
    }, 5000);
};

const message = computed(() => store.messages[props.index]);

onMounted(() => {
    startTimer();
});
onUnmounted(() => {
    stopTimer();
});
</script>
<template>
    <li :class="message.type" class="tw-bg-blue-300 tw-rounded tw-px-4 tw-py-3 tw-w-full tw-max-w-3xl">
        <span class="tw-flex tw-items-center tw-justify-between">
            <span class="tw-text-sm tw-font-semibold">{{ message.text }}</span>
            <button type="button>" @click="remove()">X</button>
        </span>
    </li>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from "vue";
import { useDmfStore } from '../stores/dmf';

const store = useDmfStore();
const props = defineProps({
    index: {
        type: Number,
        required: true
    }
});

let messageTimeout = null;

const removeMessage = () => {
    store.removeMessage(props.index);
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
        removeMessage();
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
    <li :class="message.type">
        {{ message.text }} <button type="button>" @click="removeMessage()">X</button>
    </li>
</template>

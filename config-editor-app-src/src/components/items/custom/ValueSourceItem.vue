<script setup>

import SwitchItem from '../SwitchItem.vue';

defineProps({
    currentPath: {
        type: String,
        required: true
    }
});
</script>

<template>
    <div class="i-am-value-source">
        <SwitchItem :currentPath="currentPath" />
    </div>
</template>

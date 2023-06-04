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
    <div class="i-am-evaluation">
        <SwitchItem :currentPath="currentPath" />
    </div>
</template>

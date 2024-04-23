<script setup>

import SwitchItem from '../container/switch/SwitchItem.vue';

defineProps({
    currentPath: {
        type: String,
        required: true
    }
});
</script>

<template>
    <div class="i-am-data-mapper-group">
        <SwitchItem :currentPath="currentPath" />
    </div>
</template>

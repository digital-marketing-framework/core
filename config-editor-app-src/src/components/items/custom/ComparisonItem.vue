<script setup>

import ContainerItem from '../container/container/ContainerItem.vue';

defineProps({
    currentPath: {
        type: String,
        required: true
    }
});
</script>

<template>
    <div class="i-am-comparison">
        <ContainerItem :currentPath="currentPath" />
    </div>
</template>

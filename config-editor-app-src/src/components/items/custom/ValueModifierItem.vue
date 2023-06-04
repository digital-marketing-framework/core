<script setup>

import ContainerItem from '../ContainerItem.vue';

defineProps({
    currentPath: {
        type: String,
        required: true
    }
});
</script>

<template>
    <div class="i-am-value-modifier">
        <ContainerItem :currentPath="currentPath" />
    </div>
</template>

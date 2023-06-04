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
    <div class="i-am-data-mapper">
        <ContainerItem :currentPath="currentPath" />
    </div>
</template>

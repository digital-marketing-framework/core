<script setup>

import GenericItem from '../GenericItem.vue';
import { useDmfStore } from '../../stores/dmf';

const store = useDmfStore();

defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

</script>

<template>
    <GenericItem :currentPath="store.getAbsolutePath('value', currentPath)" :dynamicItem="currentPath"/>
</template>

<script setup>

import GenericItem from '../GenericItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../../stores/dmf';

const store = useDmfStore();

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

const item = computed(() => store.getItem(props.currentPath));

</script>

<template>
    <GenericItem :currentPath="store.getAbsolutePath('value', currentPath)" :dynamicItem="item"/>
</template>

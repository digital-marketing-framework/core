<script setup>

import GenericContainerItem from './GenericContainerItem.vue';
import MapItemItem from './MapItemItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../../stores/dmf';

const store = useDmfStore();

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    },
    dynamicItem: {
        type: Object,
        required: false,
        default: null
    }
});

const childPaths = computed(() => store.getChildPaths(props.currentPath));
</script>

<template>
    <GenericContainerItem :currentPath="currentPath" :dynamicItem="dynamicItem">
        <template #fieldsUi>
            <div v-for="path in childPaths" :key="currentPath + '/' + path">
                <MapItemItem :currentPath="store.getAbsolutePath(path, currentPath)" />
            </div>
        </template>
    </GenericContainerItem>
</template>

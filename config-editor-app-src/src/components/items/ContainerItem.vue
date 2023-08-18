<script setup>

import GenericItem from '../GenericItem.vue';
import GenericContainerItem from './GenericContainerItem.vue';

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

const item = computed(() => store.getItem(props.currentPath));
</script>

<template>
    <GenericContainerItem :currentPath="currentPath" :dynamicItem="dynamicItem">
        <template #fieldsUi>
            <div v-for="path in item.childPaths" :key="currentPath + '/' + path">
                <GenericItem :currentPath="store.getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
            </div>
        </template>
    </GenericContainerItem>
</template>

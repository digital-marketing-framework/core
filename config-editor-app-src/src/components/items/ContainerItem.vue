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
            <!-- TODO container child elements can be grouped in global and secondary elements to treat those differently. it's possible to add more groups if necessary -->
            <!--
            <div v-if="item.groupedChildPaths.global" v-for="path in item.groupedChildPaths.global" :key="currentPath + '/' + path">
                <GenericItem :currentPath="store.getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
            </div>
            <div v-if="item.groupedChildPaths.secondary" v-for="path in item.groupedChildPaths.secondary" :key="currentPath + '/' + path">
                <GenericItem :currentPath="store.getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
            </div>
            -->
        </template>
    </GenericContainerItem>
</template>

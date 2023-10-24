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

const childPaths = computed(() => store.getChildPaths(props.currentPath));
// const groupedChildPaths = computed(() => store.getChildPathsGrouped(props.currentPath));
</script>

<template>
    <GenericContainerItem :currentPath="currentPath" :dynamicItem="dynamicItem">
        <template #fieldsUi>
            <div v-for="path in childPaths" :key="currentPath + '/' + path">
                <GenericItem :currentPath="store.getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
            </div>
            <!-- TODO container child elements can be grouped in global and secondary elements to treat those differently. it's possible to add more groups if necessary -->
            <!--
            <div v-if="groupedChildPaths.global" v-for="path in groupedChildPaths.global" :key="currentPath + '/' + path">
                <GenericItem :currentPath="store.getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
            </div>
            <div v-if="groupedChildPaths.secondary" v-for="path in groupedChildPaths.secondary" :key="currentPath + '/' + path">
                <GenericItem :currentPath="store.getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
            </div>
            -->
        </template>
    </GenericContainerItem>
</template>

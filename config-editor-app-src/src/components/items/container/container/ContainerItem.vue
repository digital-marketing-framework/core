<script setup>

import GenericItem from '../../GenericItem.vue';
import GenericContainerItem from '../GenericContainerItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../../../../stores/dmf';
import { getAbsolutePath } from '../../../../helpers/path';
import { usePathProcessor } from '../../../../composables/path';

const store = useDmfStore();
const { getChildPaths } = usePathProcessor(store);
// const { getChildPathsGrouped } = usePathProcessor(store);

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    },
    dynamicItemPath: {
        type: String,
        required: false,
        default: null
    }
});

const childPaths = computed(() => getChildPaths(props.currentPath));
// const groupedChildPaths = computed(() => getChildPathsGrouped(props.currentPath));
</script>

<template>
    <GenericContainerItem :currentPath="currentPath"
                          :dynamicItemPath="dynamicItemPath">
        <template #fieldsUi>
            <div v-for="path in childPaths"
                 class="tw-relative"
                 :key="currentPath + '/' + path">
                <GenericItem :currentPath="getAbsolutePath(path, currentPath)"
                             :key="currentPath + '/' + path" />
            </div>
            <!-- TODO container child elements can be grouped in global and secondary elements to treat those differently. it's possible to add more groups if necessary -->
            <!--
            <div v-if="groupedChildPaths.global" v-for="path in groupedChildPaths.global" :key="currentPath + '/' + path">
                <GenericItem :currentPath="getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
            </div>
            <div v-if="groupedChildPaths.secondary" v-for="path in groupedChildPaths.secondary" :key="currentPath + '/' + path">
                <GenericItem :currentPath="getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
            </div>
            -->
        </template>
    </GenericContainerItem>
</template>

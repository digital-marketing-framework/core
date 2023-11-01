<script setup>

import GenericContainerItem from '../GenericContainerItem.vue';
import MapElementItem from './MapElementItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../../../../stores/dmf';
import { getAbsolutePath } from '../../../../helpers/path';
import { usePathProcessor } from '../../../../composables/path';

const store = useDmfStore();
const { getChildPaths } = usePathProcessor(store);

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
</script>

<template>
    <GenericContainerItem :currentPath="currentPath"
                          :dynamicItemPath="dynamicItemPath">
        <template #fieldsUi>
            <div v-for="path in childPaths"
                 :key="currentPath + '/' + path">
                <MapElementItem :currentPath="getAbsolutePath(path, currentPath)" />
            </div>
        </template>
    </GenericContainerItem>
</template>

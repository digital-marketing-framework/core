<script setup>

import ListItemItem from './ListItemItem.vue';
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
const isIncludeList = computed(() => props.currentPath === '/metaData/includes');
const includesChanged = computed(() => store.includesChanged());
</script>

<template>
    <GenericContainerItem :currentPath="currentPath" :dynamicItem="dynamicItem">
        <template #fieldsUi>
            <div v-for="path in item.childPaths" :key="currentPath + '/' + path">
                <ListItemItem :currentPath="store.getAbsolutePath(path, currentPath)" />
            </div>
        </template>
    </GenericContainerItem>
    <button type="button"
            v-if="isIncludeList && includesChanged"
            @click="store.updateIncludes()"
            class="rounded px-4 text-sm py-1.5 disabled:opacity-50 bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
        Apply
    </button>
</template>

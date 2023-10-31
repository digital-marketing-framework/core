<script setup>

import ListElementItem from './ListElementItem.vue';
import GenericContainerItem from '../GenericContainerItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../../../../stores/dmf';
import { getAbsolutePath } from '../../../../helpers/path';
import { usePathProcessor } from '../../../../composables/path';
import { useDocument } from '../../../../composables/document';

const store = useDmfStore();
const { getChildPaths } = usePathProcessor(store);
const { isIncludeList, includesChanged, updateIncludes } = useDocument(store);

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

const includesHaveChanged = computed(() => isIncludeList(props.currentPath) && includesChanged());
const childPaths = computed(() => getChildPaths(props.currentPath));
</script>

<template>
    <GenericContainerItem :currentPath="currentPath"
                          :dynamicItemPath="dynamicItemPath">
        <template #fieldsUi>
            <div v-for="path in childPaths"
                 :key="currentPath + '/' + path">
                <ListElementItem :currentPath="getAbsolutePath(path, currentPath)" />
            </div>
        </template>
    </GenericContainerItem>
    <button type="button"
            v-if="includesHaveChanged"
            @click="updateIncludes()"
            class="rounded px-4 text-sm py-1.5 disabled:opacity-50 bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
        Apply
    </button>
</template>

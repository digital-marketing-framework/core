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
            class="tw-rounded tw-px-4 tw-text-sm tw-py-1.5 disabled:tw-opacity-50 tw-bg-blue-600 tw-font-semibold tw-text-white tw-shadow-sm hover:tw-bg-blue-500 focus-visible:tw-outline focus-visible:tw-outline-2 focus-visible:tw-outline-offset-2 focus-visible:tw-outline-blue-600">
        Apply
    </button>
</template>

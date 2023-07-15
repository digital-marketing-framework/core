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
    }
});

const item = computed(() => store.getItem(props.currentPath));
const isIncludeList = computed(() => props.currentPath === '/metaData/includes');
const includesChanged = computed(() => store.includesChanged());
</script>

<template>
    <GenericContainerItem :currentPath="currentPath">
        <template #fieldsUi>
            <div v-for="path in item.childPaths" :key="currentPath + '/' + path">
                <GenericItem :currentPath="store.getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
            </div>
            <button type="button"
                    v-if="isIncludeList && includesChanged"
                    @click="store.updateIncludes()"
                    class="rounded px-4 text-sm py-1.5 disabled:opacity-50 bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
              Apply
            </button>
        </template>
    </GenericContainerItem>
</template>

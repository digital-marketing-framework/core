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
</script>

<template>
    <GenericContainerItem :currentPath="currentPath">
        <template #fieldsUi>
            <div v-if="item.childPaths.length"
                    class="pt-3 pl-5 space-y-3">
                <div v-for="path in item.childPaths" :key="currentPath + '/' + path">
                    <GenericItem :currentPath="store.getAbsolutePath(path, currentPath)" :key="currentPath + '/' + path"/>
                </div>
            </div>
        </template>
    </GenericContainerItem>
</template>

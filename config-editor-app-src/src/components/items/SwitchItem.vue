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

const type = computed(() => store.getValue('type', props.currentPath, true));

const childPaths = computed(() => store.getChildPaths(props.currentPath).filter(childPath => childPath !== 'type' && childPath !== 'config/' + type.value));
</script>

<template>
    <GenericContainerItem :currentPath="currentPath" :dynamicItem="dynamicItem">
        <template #fieldsUi>
            <GenericItem :currentPath="store.getAbsolutePath('type', currentPath)"
                :key="store.getAbsolutePath('type', currentPath)" />
            <GenericItem v-for="childPath in childPaths"
                :key="store.getAbsolutePath(childPath, currentPath)"
                :currentPath="store.getAbsolutePath(childPath, currentPath)" />
            <GenericItem :currentPath="store.getAbsolutePath('config/' + type, currentPath)"
                :key="store.getAbsolutePath('config/' + type, currentPath)" />
        </template>
    </GenericContainerItem>
</template>

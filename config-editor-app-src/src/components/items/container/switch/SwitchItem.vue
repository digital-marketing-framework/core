<script setup>

import GenericItem from '../../GenericItem.vue';
import GenericContainerItem from '../GenericContainerItem.vue';

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

const type = computed(() => store.getValue('type', props.currentPath, true));
const childPaths = computed(() => getChildPaths(props.currentPath).filter(childPath => childPath !== 'type' && childPath !== 'config/' + type.value));
</script>

<template>
    <GenericContainerItem :currentPath="currentPath"
                          :dynamicItemPath="dynamicItemPath">
        <template #fieldsUi>
            <GenericItem :currentPath="getAbsolutePath('type', currentPath)"
                         :key="getAbsolutePath('type', currentPath)" />

            <GenericItem v-for="childPath in childPaths"
                         :key="getAbsolutePath(childPath, currentPath)"
                         :currentPath="getAbsolutePath(childPath, currentPath)" />

            <GenericItem :currentPath="getAbsolutePath('config/' + type, currentPath)"
                         :key="getAbsolutePath('config/' + type, currentPath)" />
        </template>
    </GenericContainerItem>
</template>

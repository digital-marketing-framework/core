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

const type = computed(() => store.getValue('type', props.currentPath, true));

const childPaths = computed(() => store.getChildPaths(props.currentPath).filter(childPath => childPath !== 'type' && childPath !== 'config/' + type.value));
</script>

<template>
    <GenericContainerItem :currentPath="currentPath">
        <template #fieldsUi>
            <div
                class="pt-3 pl-5 space-y-3">
                <GenericItem :currentPath="store.getAbsolutePath('type', currentPath)"
                    :isSwitchKey="true"
                    :key="currentPath + '/type'" />
                <GenericItem v-for="childPath in childPaths"
                    :key="store.getAbsolutePath(childPath, currentPath)"
                    :currentPath="store.getAbsolutePath(childPath, currentPath)" />
                <GenericItem :currentPath="store.getAbsolutePath('config/' + type, currentPath)"
                    :key="store.getAbsolutePath('config/' + type, currentPath)" />
            </div>
        </template>
    </GenericContainerItem>
</template>

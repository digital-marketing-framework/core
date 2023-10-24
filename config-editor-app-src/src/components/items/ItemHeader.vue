<script setup>
import { computed } from 'vue';

import HeaderActions from './HeaderActions.vue';

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

const parentSchema = computed(() => store.getSchema('..', props.currentPath, true));
const isDynamicItem = computed(() => store.isDynamicChild(props.currentPath));
const label = computed(() => store.getLabel(props.currentPath));
</script>

<template>
    <header class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-x-1">
            <slot name="disclosureButton"></slot>
            <label :for="'input_' + currentPath"
                   :class="{
                       'font-medium text-sm': !isDynamicItem || parentSchema.type === 'MAP',
                       'text-blue-800/50 text-xs': isDynamicItem && parentSchema.type !== 'MAP'
                   }">
                {{ label }}
            </label>
        </div>
        <HeaderActions :currentPath="currentPath" :dynamicItem="dynamicItem" />
    </header>
</template>

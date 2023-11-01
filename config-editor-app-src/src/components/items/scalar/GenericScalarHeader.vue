<script setup>
import { computed } from 'vue';

import HeaderActions from '../meta/HeaderActions.vue';

import { useDmfStore } from '../../../stores/dmf';
import { useLabelProcessor } from '../../../composables/label';
import { useDynamicProcessor } from '../../../composables/dynamicItem';

const store = useDmfStore();
const { getLabel } = useLabelProcessor(store);
const { isDynamicChild } = useDynamicProcessor(store);

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

const parentSchema = computed(() => store.getSchema('..', props.currentPath, true));
const isDynamicItem = computed(() => isDynamicChild(props.currentPath));
const label = computed(() => getLabel(props.currentPath));
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
        <HeaderActions :currentPath="currentPath"
                       :dynamicItemPath="dynamicItemPath" />
    </header>
</template>

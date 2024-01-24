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
    <header class="tw-flex tw-items-center tw-justify-between tw-gap-4">
        <div class="tw-flex tw-items-center tw-gap-x-1">
            <slot name="disclosureButton"></slot>
            <label :for="'input_' + currentPath"
                   :class="{
                       'tw-font-medium tw-text-sm': !isDynamicItem || parentSchema.type === 'MAP',
                       'tw-text-blue-800/50 tw-text-xs': isDynamicItem && parentSchema.type !== 'MAP'
                   }">
                {{ label }}
            </label>
        </div>
        <HeaderActions :currentPath="currentPath"
                       :dynamicItemPath="dynamicItemPath" />
    </header>
</template>

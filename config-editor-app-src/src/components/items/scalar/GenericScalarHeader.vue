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
const schema = computed(() => store.getSchema(props.currentPath, undefined, true));
const description = computed(() => schema.value.description || 'Additional data can be provided for outbound routes, like form submissions. The data is derived from the context of the request that triggered the route, like the website language or the timestamp or request cookies or headers.');
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
    <div class="tw-pt-3 tw-text-xs tw-text-indigo-800 tw-opacity-80"
        v-if="description">{{ description }}
    </div>
</template>

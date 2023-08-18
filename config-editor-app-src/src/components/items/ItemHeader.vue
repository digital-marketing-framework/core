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

const item = computed(() => store.getItem(props.currentPath));

const parentItem = computed(() => store.getParentItem(props.currentPath));
</script>

<template>
    <header class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-x-1">
            <slot name="disclosureButton"></slot>
            <label :for="'input_' + currentPath"
                   :class="{
                       'font-medium text-sm': !item.isDynamicItem || parentItem.schema.type === 'MAP',
                       'text-blue-800/50 text-xs': item.isDynamicItem && parentItem.schema.type !== 'MAP'
                   }">
                {{ item.label }}
            </label>
        </div>
        <HeaderActions :currentPath="currentPath" :dynamicItem="dynamicItem" />
    </header>
</template>

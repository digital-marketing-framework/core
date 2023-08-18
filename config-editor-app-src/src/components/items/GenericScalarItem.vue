<script setup>
import ItemHeader from './ItemHeader.vue';
import RawItem from './RawItem.vue';

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

const item = computed(() => store.getItem(props.currentPath));
</script>
<template>
    <slot name="fieldHeader" v-if="!item.schema.skipHeader">
        <ItemHeader :currentPath="currentPath" :dynamicItem="dynamicItem" />
    </slot>
    <slot v-if="!item.rawView" name="fieldUi"></slot>
    <RawItem v-else :currentPath="currentPath" />
    <slot name="fieldFooter"></slot>
</template>

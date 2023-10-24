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

const schema = computed(() => store.getSchema(props.currentPath, undefined, true));
const rawView = computed(() => store.isRawView(props.currentPath));
</script>
<template>
    <slot name="fieldHeader" v-if="!schema.skipHeader">
        <ItemHeader :currentPath="currentPath" :dynamicItem="dynamicItem" />
    </slot>
    <slot v-if="!rawView" name="fieldUi"></slot>
    <RawItem v-else :currentPath="currentPath" />
    <slot name="fieldFooter"></slot>
</template>

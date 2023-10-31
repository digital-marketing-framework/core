<script setup>
import GenericScalarHeader from './GenericScalarHeader.vue';
import RawItem from '../RawItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../../../stores/dmf';
import { useRawProcessor } from '../../../composables/raw';

const store = useDmfStore();
const { isRawView } = useRawProcessor(store);

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

const schema = computed(() => store.getSchema(props.currentPath, undefined, true));
const rawView = computed(() => isRawView(props.currentPath));
</script>
<template>
    <slot name="fieldHeader"
          v-if="!schema.skipHeader">
        <GenericScalarHeader :currentPath="currentPath"
                             :dynamicItemPath="dynamicItemPath" />
    </slot>
    <slot v-if="!rawView"
          name="fieldUi"></slot>
    <RawItem v-else
             :currentPath="currentPath" />
    <slot name="fieldFooter"></slot>
</template>

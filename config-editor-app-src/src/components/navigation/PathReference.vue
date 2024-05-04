<script setup>
import { computed } from "vue";
import { useDmfStore } from '@/stores/dmf';
import { useLabelProcessor } from '@/composables/label';
import { usePathProcessor } from '@/composables/path';
import ItemIcon from '@/components/icons/ItemIcon.vue';

const store = useDmfStore();
const { processLabel } = useLabelProcessor(store);
const { selectPath } = usePathProcessor(store);

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    },
    referencePath: {
        type: String,
        required: true
    },
    referenceLabel: {
        type: String,
        required: false,
        default: null
    },
    referenceIcon: {
        type: String,
        required: false,
        default: null
    }
});

const icon = computed(() => props.referenceIcon || '');
const label = computed(() => props.referenceLabel ? processLabel(props.referenceLabel, props.referencePath, props.currentPath, true) : '');
</script>

<template>
    <span @click="selectPath(referencePath, currentPath)">
        <span v-if="label">{{ label }}</span>
        <span v-if="icon">
            <ItemIcon item-type="reference" :custom-icon="icon" />
        </span>
    </span>
</template>

<script setup>
import { computed } from "vue";
import { useDmfStore } from '@/stores/dmf';
import { useLabelProcessor } from '@/composables/label';
import { usePathProcessor } from '@/composables/path';
import ItemIcon from '@/components/icons/ItemIcon.vue';

const store = useDmfStore();
const { processLabel } = useLabelProcessor(store);
const { selectPath, processPathPattern } = usePathProcessor(store);

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

const path = computed(() => processPathPattern(props.referencePath, props.currentPath));
const icon = computed(() => props.referenceIcon || '');
const label = computed(() => {
    if (!props.referenceLabel || !path.value) {
        return '';
    }
    const result = processLabel(props.referenceLabel, path.value, props.currentPath, true);
    return result || '';
});
const valid = computed(() => path.value && (label.value || icon.value));
</script>

<template>
    <span v-if="valid" @click="selectPath(path, currentPath)">
        <span v-if="label">{{ label }}</span>
        <span v-if="icon">
            <ItemIcon item-type="reference" :custom-icon="icon" />
        </span>
    </span>
</template>

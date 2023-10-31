<script setup>
// import { storeToRefs } from 'pinia';
import { computed } from "vue";
import { useDmfStore } from '../../../stores/dmf';
import { getLevel } from '../../../helpers/path';
import {
    isCustomType as _isCustomType,
    isContainerType,
    isDynamicContainerType,
    isScalarType
} from "../../../helpers/type";
import { useLabelProcessor } from "../../../composables/label";
import { usePathProcessor } from "../../../composables/path";

const store = useDmfStore();
const { getLabel } = useLabelProcessor(store);
const { isSelected } = usePathProcessor(store);

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

const immediateSchema = computed(() => store.getSchema(props.currentPath));
const schema = computed(() => store.resolveSchema(immediateSchema.value));
const isScalar = computed(() => isScalarType(schema.value.type));
const isContainer = computed(() => isContainerType(schema.value.type));
const isDynamicContainer = computed(() => isDynamicContainerType(schema.value.type));
const isCustomType = computed(() => _isCustomType(immediateSchema.value.type));
const triggers = computed(() => store.getTriggers(props.currentPath));
const level = computed(() => getLevel(props.currentPath));
const label = computed(() => getLabel(props.currentPath));
const selected = computed(() => isSelected(props.currentPath));
const isOverwritten = computed(() => store.isOverwritten(props.currentPath));
const value = computed(() => isScalar.value ? store.getValue(props.currentPath) : null);

</script>

<template>
    <div class="text-xs text-left text-gray-500">
        <table>
            <tr>
                <th class="p-1 align-top">Path</th>
                <td class="p-1 align-top"><span class="break-all">{{ currentPath }}</span></td>
            </tr>
            <tr>
                <th class="p-1 align-top">Label</th>
                <td class="p-1 align-top">{{ label }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Type</th>
                <td class="p-1 align-top">{{ schema.type }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Level</th>
                <td class="p-1 align-top">{{ level }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Selected</th>
                <td class="p-1 align-top">{{ selected }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Overwritten</th>
                <td class="p-1 align-top">{{ isOverwritten }}</td>
            </tr>
            <tr v-if="isScalar">
                <th class="p-1 align-top">Value</th>
                <td class="p-1 align-top"><span class="break-all">{{ value }}</span></td>
            </tr>
            <tr v-if="isContainer">
                <th class="p-1 align-top">Dynamic Container</th>
                <td class="p-1 align-top">{{ isDynamicContainer }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Dynamic Item</th>
                <td class="p-1 align-top">{{ !!dynamicItemPath }}</td>
            </tr>
            <tr v-if="isCustomType">
                <th class="p-1 align-top">Immediate Schema</th>
                <td class="p-1 align-top">{{ immediateSchema }}</td>
            </tr>
            <tr v-if="isScalar">
                <th class="p-1 align-top">Schema</th>
                <td class="p-1 align-top">{{ schema }}</td>
            </tr>
            <tr v-if="triggers.length > 0">
                <th class="p-1 align-top">Triggers</th>
                <td class="p1-align-top">{{ triggers }}</td>
            </tr>
        </table>
    </div>
</template>

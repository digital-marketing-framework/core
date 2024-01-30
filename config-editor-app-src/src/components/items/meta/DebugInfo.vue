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
    <div class="tw-text-xs tw-text-left tw-text-gray-500">
        <table>
            <tr>
                <th class="tw-p-1 tw-align-top">Path</th>
                <td class="tw-p-1 tw-align-top"><span class="tw-break-all">{{ currentPath }}</span></td>
            </tr>
            <tr>
                <th class="tw-p-1 tw-align-top">Label</th>
                <td class="tw-p-1 tw-align-top">{{ label }}</td>
            </tr>
            <tr>
                <th class="tw-p-1 tw-align-top">Type</th>
                <td class="tw-p-1 tw-align-top">{{ schema.type }}</td>
            </tr>
            <tr>
                <th class="tw-p-1 tw-align-top">Level</th>
                <td class="tw-p-1 tw-align-top">{{ level }}</td>
            </tr>
            <tr>
                <th class="tw-p-1 tw-align-top">Selected</th>
                <td class="tw-p-1 tw-align-top">{{ selected }}</td>
            </tr>
            <tr>
                <th class="tw-p-1 tw-align-top">Overwritten</th>
                <td class="tw-p-1 tw-align-top">{{ isOverwritten }}</td>
            </tr>
            <tr v-if="isScalar">
                <th class="tw-p-1 tw-align-top">Value</th>
                <td class="tw-p-1 tw-align-top"><span class="tw-break-all">{{ value }}</span></td>
            </tr>
            <tr v-if="isContainer">
                <th class="tw-p-1 tw-align-top">Dynamic Container</th>
                <td class="tw-p-1 tw-align-top">{{ isDynamicContainer }}</td>
            </tr>
            <tr>
                <th class="tw-p-1 tw-align-top">Dynamic Item</th>
                <td class="tw-p-1 tw-align-top">{{ !!dynamicItemPath }}</td>
            </tr>
            <tr v-if="isCustomType">
                <th class="tw-p-1 tw-align-top">Immediate Schema</th>
                <td class="tw-p-1 tw-align-top">{{ immediateSchema }}</td>
            </tr>
            <tr v-if="isScalar">
                <th class="tw-p-1 tw-align-top">Schema</th>
                <td class="tw-p-1 tw-align-top">{{ schema }}</td>
            </tr>
            <tr v-if="triggers.length > 0">
                <th class="tw-p-1 tw-align-top">Triggers</th>
                <td class="tw-p-1 tw-align-top">{{ triggers }}</td>
            </tr>
        </table>
    </div>
</template>

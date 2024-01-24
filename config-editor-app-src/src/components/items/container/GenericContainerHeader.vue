<script setup>
import ItemIcon from '../../icons/ItemIcon.vue';
import HeaderActions from '../meta/HeaderActions.vue';

import { computed } from "vue";
import { useDmfStore } from '../../../stores/dmf';
import { useLabelProcessor } from '../../../composables/label';

const store = useDmfStore();
const { getLabel } = useLabelProcessor(store);

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
const isOverwritten = computed(() => store.isOverwritten(props.currentPath));
const label = computed(() => getLabel(props.currentPath));
</script>

<template>
    <header class="tw-flex tw-items-center tw-justify-between tw-gap-4 tw-px-3 tw-py-2 tw-text-indigo-800 tw-bg-indigo-100 tw-border tw-rounded tw-border-indigo-500/20"
            :class="{
                'tw-border-r-red-400 todo-class-overwritten': isOverwritten
            }">
        <div class="tw-flex tw-items-center tw-gap-x-2">
            <slot name="disclosureButton"></slot>
            <label :for="label"
                   class="tw-flex tw-items-center tw-text-sm tw-font-medium">
                <ItemIcon :item-type="schema.type"
                          class="!tw-text-indigo-800 tw-mr-2.5" />
                <span>{{ label }}</span>
            </label>
        </div>
        <HeaderActions :currentPath="currentPath"
                       :dynamicItemPath="dynamicItemPath" />
    </header>
</template>

<script setup>
import { computed } from 'vue';
import { useDmfStore } from '../../../stores/dmf';
import { getLeafKey } from '../../../helpers/path';
import { useLabelProcessor } from '../../../composables/label';

import GenericScalarItem from './GenericScalarItem.vue';
import HeaderActions from '../meta/HeaderActions.vue';

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

const currentKey = computed(() => getLeafKey(props.currentPath));
const schema = computed(() => store.getSchema(props.currentPath, undefined, true));
const parentValue = computed(() => store.getParentValue(props.currentPath));
const label = computed(() => getLabel(props.currentPath));
</script>

<template>
    <GenericScalarItem :currentPath="currentPath"
                       :dynamicItemPath="dynamicItemPath">
        <template #fieldHeader><span><!-- a slot cannot be overwritten with empty content --></span></template>
        <template #fieldUi>
            <div class="tw-relative tw-flex tw-items-start">
                <div class="tw-flex tw-items-center tw-h-6">
                    <input :id="'input_' + currentPath"
                           :name="'input_' + currentPath"
                           v-model="parentValue[currentKey]"
                           type="checkbox"
                           autocomplete="off"
                           class="tw-form-checkbox tw-w-4 tw-h-4 tw-text-blue-600 tw-border-blue-200 tw-rounded focus:tw-ring-blue-600"
                           :class="{
                               'todo-class-readonly tw-bg-neutral-100': store.settings.readonly
                           }"
                           :disabled="store.settings.readonly">
                </div>
                <div class="tw-flex tw-justify-between tw-ml-3 tw-text-sm tw-leading-6 tw-gap-x-2 tw-grow">
                    <label :for="'input_' + currentPath"
                           class="tw-font-medium">{{ label }}</label>
                </div>
            </div>
        </template>
        <template #fieldFooter>
            <HeaderActions v-if="!schema.skipHeader"
                           :currentPath="currentPath"
                           :dynamicItemPath="dynamicItemPath" />
        </template>
    </GenericScalarItem>
</template>

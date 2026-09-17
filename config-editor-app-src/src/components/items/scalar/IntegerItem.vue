<script setup>
import { computed } from 'vue';
import { useDmfStore } from '../../../stores/dmf';
import { getLeafKey } from '../../../helpers/path';

import GenericScalarItem from './GenericScalarItem.vue';

const store = useDmfStore();

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
</script>
<template>
    <GenericScalarItem :currentPath="currentPath"
                       :dynamicItemPath="dynamicItemPath">
        <template #fieldUi>
            <div class="tw-mt-2">
                <input v-if="schema.format === 'hidden'"
                       :id="'input_' + currentPath"
                       :name="'input_' + currentPath"
                       v-model="parentValue[currentKey]"
                       type="hidden" />
                <input v-else
                       :id="'input_' + currentPath"
                       :name="'input_' + currentPath"
                       v-model="parentValue[currentKey]"
                       type="number"
                       autocomplete="off"
                       placeholder="Enter value"
                       class="tw-form-input tw-block tw-w-full tw-rounded tw-border-0 tw-py-1.5 tw-text-ink tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-input placeholder:tw-text-placeholder focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-focus sm:tw-text-sm sm:tw-leading-6"
                       :class="{
                           'custom-class-readonly tw-bg-surface-readonly': store.settings.readonly,
                           'tw-bg-surface': !store.settings.readonly
                       }"
                       :disabled="store.settings.readonly" />
            </div>
        </template>
    </GenericScalarItem>
</template>

<script setup>
import { computed } from 'vue';
import { useDmfStore } from '../../../../stores/dmf';
import { useValueSets } from '../../../../composables/valueSets';

import GenericScalarItem from '../../scalar/GenericScalarItem.vue';

const store = useDmfStore();
const { getAllowedValues } = useValueSets(store);

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

const value = computed(() => store.getValue(props.currentPath));
const allowedValues = computed(() => getAllowedValues(props.currentPath));
</script>

<template>
    <GenericScalarItem :currentPath="currentPath">
        <template #fieldUi>
            <div class="tw-mt-2">
                <select :value="value"
                        @change="store.setValue(currentPath, undefined, $event.target.value, true)"
                        class="tw-form-select tw-block tw-w-full tw-rounded tw-border-0 tw-py-1.5 tw-text-gray-900 placeholder:tw-text-blue-800 placeholder:tw-opacity-60 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-blue-200 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-blue-600 sm:tw-text-sm sm:tw-leading-6 tw-text-ellipsis"
                        :class="{
                            'custom-class-readonly tw-bg-neutral-100': store.settings.readonly
                        }"
                        :disabled="store.settings.readonly">
                    <option v-for="(label, value) in allowedValues"
                            :key="value"
                            :value="value">{{ label }}</option>
                </select>
            </div>
        </template>
    </GenericScalarItem>
</template>

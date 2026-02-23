<script setup>
import { computed, nextTick, ref } from 'vue';
import { useDmfStore } from '../../../stores/dmf';
import { useValueSets } from '../../../composables/valueSets';
import { getLeafKey } from '../../../helpers/path';

import GenericScalarItem from './GenericScalarItem.vue';

const store = useDmfStore();
const { getAllowedValues, getSuggestedValues } = useValueSets(store);

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
const currentValue = computed(() => parentValue.value[currentKey.value]);
const allowedValues = computed(() => getAllowedValues(props.currentPath));
const invalidValue = computed(() => Object.keys(allowedValues.value).indexOf(currentValue.value) === -1);

const suggestedValues = computed(() => getSuggestedValues(props.currentPath));
const isValueSuggested = computed(() => currentValue.value !== '' && currentValue.value in suggestedValues.value);
const showSuggestions = ref(false);
const filteredSuggestions = computed(() => {
    const input = (currentValue.value || '').toLowerCase();
    const result = {};
    for (const [value, label] of Object.entries(suggestedValues.value)) {
        if (!input || value.toLowerCase().includes(input) || String(label).toLowerCase().includes(input)) {
            result[value] = label;
        }
    }
    return result;
});
const hasSuggestions = computed(() => Object.keys(filteredSuggestions.value).length > 0);

const editingCombobox = ref(false);
const comboboxInputRef = ref(null);

function selectSuggestion(value) {
    parentValue.value[currentKey.value] = value;
    showSuggestions.value = false;
    editingCombobox.value = false;
}

function editComboboxValue() {
    if (!store.settings.readonly) {
        editingCombobox.value = true;
        nextTick(() => comboboxInputRef.value?.focus());
    }
}
</script>

<template>
    <GenericScalarItem :currentPath="currentPath"
                       :dynamicItemPath="dynamicItemPath">
        <template #fieldUi>
            <div class="tw-mt-2">
                <select v-if="schema.format === 'select'"
                        v-model="parentValue[currentKey]"
                        class="tw-form-select tw-block tw-w-full tw-rounded tw-border-0 tw-py-1.5 tw-text-gray-900 placeholder:tw-text-blue-800 placeholder:tw-opacity-60 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-blue-200 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-blue-600 sm:tw-text-sm sm:tw-leading-6 tw-text-ellipsis"
                        :class="{
                            'custom-class-readonly tw-bg-neutral-100': store.settings.readonly
                        }"
                        :disabled="store.settings.readonly">
                    <option v-if="invalidValue"
                            :value="currentValue">INVALID VALUE "{{ currentValue }}"</option>
                    <option v-for="(label, value) in allowedValues"
                            :key="value"
                            :value="value">{{ label }}</option>
                </select>
                <textarea v-else-if="schema.format === 'text'"
                          v-model="parentValue[currentKey]"
                          class="tw-form-textarea tw-block tw-w-full tw-rounded tw-border-0 tw-py-1.5 tw-text-gray-900 placeholder:tw-text-blue-800 placeholder:tw-opacity-60 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-blue-200 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-blue-600 sm:tw-text-sm sm:tw-leading-6"
                          :class="{
                              'custom-class-readonly tw-bg-neutral-100': store.settings.readonly
                          }" />
                <input v-else-if="schema.format === 'hidden'"
                       :id="'input_' + currentPath"
                       :name="'input_' + currentPath"
                       v-model="parentValue[currentKey]"
                       type="hidden" />
                <div v-else-if="schema.format === 'combobox'" class="tw-relative" :class="{ 'tw-z-50': showSuggestions }">
                    <div v-if="currentValue && isValueSuggested && !editingCombobox"
                         class="tw-flex tw-items-center tw-gap-1">
                        <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-rounded tw-border tw-border-blue-300 tw-bg-blue-50 tw-px-2.5 tw-py-1.5 tw-text-sm tw-text-gray-900 tw-cursor-pointer hover:tw-bg-blue-100"
                              @click="editComboboxValue">
                            {{ suggestedValues[currentValue] }}
                            <span class="tw-text-xs tw-text-gray-500">({{ currentValue }})</span>
                        </span>
                    </div>
                    <div v-else>
                        <input ref="comboboxInputRef"
                               v-model="parentValue[currentKey]"
                               :id="'input_' + currentPath"
                               :name="'input_' + currentPath"
                               type="text"
                               autocomplete="off"
                               placeholder="Enter value"
                               @focus="showSuggestions = true"
                               @blur="showSuggestions = false; editingCombobox = false"
                               class="tw-form-input tw-block tw-w-full tw-rounded tw-border-0 tw-py-1.5 tw-text-gray-900 placeholder:tw-text-blue-800 placeholder:tw-opacity-60 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-blue-200 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-blue-600 sm:tw-text-sm sm:tw-leading-6"
                               :class="{
                                   'custom-class-readonly tw-bg-neutral-100': store.settings.readonly
                               }"
                               :disabled="store.settings.readonly" />
                        <ul v-if="showSuggestions && hasSuggestions"
                            class="combobox-suggestions tw-absolute tw-z-10 tw-mt-1 tw-max-h-60 tw-w-full tw-overflow-auto tw-rounded tw-bg-white tw-py-1 tw-shadow-lg tw-ring-1 tw-ring-black/5">
                            <li v-for="(label, value) in filteredSuggestions"
                                :key="value"
                                @mousedown.prevent="selectSuggestion(value)"
                                class="tw-cursor-pointer tw-px-3 tw-py-2 tw-text-sm hover:tw-bg-blue-50">
                                {{ label }}
                                <span v-if="label !== value" class="tw-text-xs tw-text-gray-400 tw-ml-1">({{ value }})</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <input v-else
                       :id="'input_' + currentPath"
                       :name="'input_' + currentPath"
                       v-model="parentValue[currentKey]"
                       type="text"
                       autocomplete="off"
                       placeholder="Enter value"
                       class="tw-form-input tw-block tw-w-full tw-rounded tw-border-0 tw-py-1.5 tw-text-gray-900 placeholder:tw-text-blue-800 placeholder:tw-opacity-60 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-blue-200 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-blue-600 sm:tw-text-sm sm:tw-leading-6"
                       :class="{
                           'custom-class-readonly tw-bg-neutral-100': store.settings.readonly
                       }"
                       :disabled="store.settings.readonly" />
            </div>
        </template>
    </GenericScalarItem>
</template>

<script setup>
import { computed } from 'vue';
import { useDmfStore } from '../../../stores/dmf';
import { useValueSets } from '../../../composables/valueSets';
import { getLeafKey } from '../../../helpers/path';

import GenericScalarItem from './GenericScalarItem.vue';

const store = useDmfStore();
const { getAllowedValues } = useValueSets(store);

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
</script>

<template>
    <GenericScalarItem :currentPath="currentPath"
                       :dynamicItemPath="dynamicItemPath">
        <template #fieldUi>
            <div class="mt-2">
                <select v-if="schema.format === 'select'"
                        v-model="parentValue[currentKey]"
                        class="block w-full rounded border-0 py-1.5 text-gray-900 placeholder:text-blue-800 placeholder:opacity-60 shadow-sm ring-1 ring-inset ring-blue-200 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 text-ellipsis"
                        :class="{
                            'todo-class-readonly bg-neutral-100': store.settings.readonly
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
                          class="block w-full rounded border-0 py-1.5 text-gray-900 placeholder:text-blue-800 placeholder:opacity-60 shadow-sm ring-1 ring-inset ring-blue-200 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                          :class="{
                              'todo-class-readonly bg-neutral-100': store.settings.readonly
                          }" />
                <input v-else-if="schema.format === 'hidden'"
                       :id="'input_' + currentPath"
                       :name="'input_' + currentPath"
                       v-model="parentValue[currentKey]"
                       type="hidden" />
                <input v-else
                       :id="'input_' + currentPath"
                       :name="'input_' + currentPath"
                       v-model="parentValue[currentKey]"
                       type="text"
                       autocomplete="off"
                       placeholder="Enter value"
                       class="block w-full rounded border-0 py-1.5 text-gray-900 placeholder:text-blue-800 placeholder:opacity-60 shadow-sm ring-1 ring-inset ring-blue-200 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                       :class="{
                           'todo-class-readonly bg-neutral-100': store.settings.readonly
                       }"
                       :disabled="store.settings.readonly" />
            </div>
        </template>
    </GenericScalarItem>
</template>

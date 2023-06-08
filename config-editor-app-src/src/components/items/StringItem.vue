<script setup>
import { computed } from 'vue';
import { useDmfStore } from '../../stores/dmf';

import GenericScalarItem from './GenericScalarItem.vue';

const store = useDmfStore();

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

const item = computed(() => store.getItem(props.currentPath));
</script>

<template>
    <GenericScalarItem :currentPath="currentPath">
        <template #fieldUi>
            <div class="mt-2">
                <select v-if="item.schema.format === 'select'"
                    v-model="item.parentValue[item.currentKey]">
                    <option v-for="(label, value) in store.getAllowedValues(currentPath)" :key="value" :value="value">{{ label }}</option>
                </select>
                <input v-else
                    :id="'input_' + currentPath"
                    :name="'input_' + currentPath"
                    v-model="item.parentValue[item.currentKey]"
                    type="text"
                    autocomplete="off"
                    placeholder="Enter value"
                    class="block w-full rounded border-0 py-1.5 text-gray-900 placeholder:text-blue-800 placeholder:opacity-60 shadow-sm ring-1 ring-inset ring-blue-200 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
            </div>
        </template>
    </GenericScalarItem>
</template>

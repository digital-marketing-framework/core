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
            <div class="mt-2">
                <select :value="value"
                        @change="store.setValue(currentPath, undefined, $event.target.value, true)"
                        class="block w-full rounded border-0 py-1.5 text-gray-900 placeholder:text-blue-800 placeholder:opacity-60 shadow-sm ring-1 ring-inset ring-blue-200 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 text-ellipsis"
                        :class="{
                            'todo-class-readonly bg-neutral-100': store.settings.readonly
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

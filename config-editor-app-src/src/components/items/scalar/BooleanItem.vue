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
            <div class="relative flex items-start">
                <div class="flex items-center h-6">
                    <input :id="'input_' + currentPath"
                           :name="'input_' + currentPath"
                           v-model="parentValue[currentKey]"
                           type="checkbox"
                           autocomplete="off"
                           class="w-4 h-4 text-blue-600 border-blue-200 rounded focus:ring-blue-600"
                           :class="{
                               'todo-class-readonly bg-neutral-100': store.settings.readonly
                           }"
                           :disabled="store.settings.readonly">
                </div>
                <div class="flex justify-between ml-3 text-sm leading-6 gap-x-2 grow">
                    <label :for="'input_' + currentPath"
                           class="font-medium">{{ label }}</label>
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

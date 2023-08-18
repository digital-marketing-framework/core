<script setup>
import GenericScalarItem from './GenericScalarItem.vue';
import HeaderActions from './HeaderActions.vue';

import { computed } from 'vue';
import { useDmfStore } from '../../stores/dmf';

const store = useDmfStore();

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    },
    dynamicItem: {
        type: Object,
        required: false,
        default: null
    }
});

const item = computed(() => store.getItem(props.currentPath));
</script>

<template>
    <GenericScalarItem :currentPath="currentPath" :dynamicItem="dynamicItem">
        <template #fieldHeader><span><!-- a slot cannot be overwritten with empty content --></span></template>
        <template #fieldUi>
            <div class="relative flex items-start">
                <div class="flex items-center h-6">
                    <input :id="'input_' + currentPath"
                        :name="'input_' + currentPath"
                        v-model="item.parentValue[item.currentKey]"
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
                        class="font-medium">{{ item.label }}</label>
                </div>
            </div>
        </template>
        <template #fieldFooter>
            <HeaderActions v-if="!item.schema.skipHeader" :currentPath="currentPath" :dynamicItem="dynamicItem"/>
        </template>
    </GenericScalarItem>
</template>

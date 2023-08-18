<script setup>
import ItemIcon from '../icons/ItemIcon.vue';
import HeaderActions from './HeaderActions.vue';

import { computed } from "vue";
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
    <header
            class="flex items-center justify-between gap-4 px-3 py-2 text-indigo-800 bg-indigo-100 border rounded border-indigo-500/20"
            :class="{
                'border-r-red-400 todo-class-overwritten': item.isOverwritten
            }">
        <div class="flex items-center gap-x-2">
            <slot name="disclosureButton"></slot>
            <label :for="item.label"
                   class="flex items-center text-sm font-medium">
                <ItemIcon :item-type="item.schema.type"
                          class="!text-indigo-800 mr-2.5" />
                <span>{{ item.label }}</span>
            </label>
        </div>
        <HeaderActions :currentPath="currentPath" :dynamicItem="dynamicItem" />
    </header>
</template>

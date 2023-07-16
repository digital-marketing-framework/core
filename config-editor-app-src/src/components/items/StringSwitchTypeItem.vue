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
                <select :value="item.value"
                    @change="store.setValue(currentPath, currentPath, $event.target.value, true)"
                    :class="{
                        'todo-class-readonly bg-neutral-100': store.settings.readonly
                    }"
                    :disabled="store.settings.readonly">
                    <option v-for="(label, value) in store.getAllowedValues(currentPath)" :key="value" :value="value">{{ label }}</option>
                </select>
            </div>
        </template>
    </GenericScalarItem>
</template>

<script setup>

import { computed } from 'vue';

import GenericItem from '../GenericItem.vue';
import GenericContainerItem from './GenericContainerItem.vue';

import { useDmfStore } from '../../stores/dmf';

const store = useDmfStore();

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

const valueSchema = computed(() => store.getSchema('value', props.currentPath, true));
const valueIsScalar = computed(() => store.isScalarType(valueSchema.value.type));
</script>

<template>
    <GenericContainerItem :currentPath="currentPath">
        <template #fieldsUi>
            <GenericItem :currentPath="store.getAbsolutePath('key', currentPath)" />
            <GenericItem :currentPath="store.getAbsolutePath('value', currentPath)" :dynamicItem="currentPath" :class="{
                'todo-scalar-map-value-maybe-easy-to-put-side-by-side': valueIsScalar,
                'todo-complex-map-value-maybe-harder-to-put-side-by-side': !valueIsScalar
            }" />
        </template>
    </GenericContainerItem>
</template>

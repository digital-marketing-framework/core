<script setup>

import GenericItem from '../GenericItem.vue';
import GenericContainerItem from './GenericContainerItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../../stores/dmf';

const store = useDmfStore();

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

const item = computed(() => store.getItem(props.currentPath));
const valueItem = computed(() => store.getItem('value', props.currentPath));

</script>

<template>
    <GenericContainerItem :currentPath="currentPath">
        <template #fieldsUi>
            <GenericItem :currentPath="store.getAbsolutePath('key', currentPath)" />
            <GenericItem :currentPath="store.getAbsolutePath('value', currentPath)" :dynamicItem="item" :class="{
                'todo-scalar-map-value-maybe-easy-to-put-side-by-side': valueItem.isScalar,
                'todo-complex-map-value-maybe-harder-to-put-side-by-side': !valueItem.isScalar
            }" />
        </template>
    </GenericContainerItem>
</template>

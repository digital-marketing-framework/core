<script setup>

import ContainerItem from './items/ContainerItem.vue';
import ListItem from './items/ListItem.vue';
import MapItem from './items/MapItem.vue';
import SwitchItem from './items/SwitchItem.vue';
import StringSwitchTypeItem from './items/StringSwitchTypeItem.vue';
import StringItem from './items/StringItem.vue';
import IntegerItem from './items/IntegerItem.vue';
import BooleanItem from './items/BooleanItem.vue';

// import RouteItem from './items/custom/RouteItem.vue';
// import ValueItem from './items/custom/ValueItem.vue';
// import ValueSourceItem from './items/custom/ValueSourceItem.vue';
// import ValueModifierItem from './items/custom/ValueModifierItem.vue';
// import EvaluationItem from './items/custom/EvaluationItem.vue';
// import ComparisonItem from './items/custom/ComparisonItem.vue';
// import DataMapperItem from './items/custom/DataMapperItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../stores/dmf';

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

const immediateSchema = computed(() => store.getSchema(props.currentPath));
const schema = computed(() => store.resolveSchema(immediateSchema.value));
const isContainer = computed(() => store.isContainerType(schema.value.type));
const triggers = computed(() => store.getTriggers(props.currentPath));
const isVisible = computed(() => store.isVisible(props.currentPath));
const isOverwritten = computed(() => store.isOverwritten(props.currentPath));
const hasIssues = computed(() => store.hasIssues(props.currentPath));
const issue = computed(() => store.getIssue(props.currentPath));
</script>
<template>
    <div class="w-full max-w-3xl"
         :class="{
             'bg-blue-100 text-blue-800 border border-blue-200 py-2 px-3 rounded': !isContainer,
             'border-r-red-400 todo-class-overwritten': !isContainer && isOverwritten
         }"
         v-if="isVisible">

<!-- use if needed: -->
<!--
        <DataMapperItem v-if="immediateSchema.type === 'DATA_MAPPER'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <ValueSourceItem v-else-if="immediateSchema.type === 'VALUE_SOURCE'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <ValueModifierItem v-else-if="immediateSchema.type === 'VALUE_MODIFIER'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <ValueItem v-else-if="immediateSchema.type === 'VALUE'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <RouteItem v-else-if="immediateSchema.type === 'ROUTE'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <EvaluationItem v-else-if="immediateSchema.type === 'EVALUATION'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <ComparisonItem v-else-if="immediateSchema.type === 'COMPARISON'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
-->

        <ContainerItem v-if="schema.type === 'CONTAINER'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <ListItem v-else-if="schema.type === 'LIST'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <MapItem v-else-if="schema.type === 'MAP'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <SwitchItem v-else-if="schema.type === 'SWITCH'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <StringSwitchTypeItem v-else-if="schema.type === 'STRING' && triggers.indexOf('switch') >= 0" :currentPath="currentPath" />
        <StringItem v-else-if="schema.type === 'STRING'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <IntegerItem v-else-if="schema.type === 'INTEGER'" :currentPath="currentPath" :dynamicItem="dynamicItem" />
        <BooleanItem v-else-if="schema.type === 'BOOLEAN'" :currentPath="currentPath" :dynamicItem="dynamicItem" />

        <div v-if="hasIssues">{{ issue }}</div>
    </div>
</template>

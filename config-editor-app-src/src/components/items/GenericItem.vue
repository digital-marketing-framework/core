<script setup>

import ContainerItem from './container/container/ContainerItem.vue';
import ListItem from './container/list/ListItem.vue';
import MapItem from './container/map/MapItem.vue';
import SwitchItem from './container/switch/SwitchItem.vue';
import StringSwitchTypeItem from './container/switch/StringSwitchTypeItem.vue';
import StringItem from './scalar/StringItem.vue';
import IntegerItem from './scalar/IntegerItem.vue';
import BooleanItem from './scalar/BooleanItem.vue';

// import RouteItem from './custom/RouteItem.vue';
// import ValueItem from './custom/ValueItem.vue';
// import ValueSourceItem from './custom/ValueSourceItem.vue';
// import ValueModifierItem from './custom/ValueModifierItem.vue';
// import EvaluationItem from './custom/EvaluationItem.vue';
// import ComparisonItem from './custom/ComparisonItem.vue';
// import DataMapperItem from './custom/DataMapperItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../../stores/dmf';
import { useValidation } from '../../composables/validation';
import { isContainerType } from '../../helpers/type';

const store = useDmfStore();
const { hasIssues, getIssue } = useValidation(store);

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

const immediateSchema = computed(() => store.getSchema(props.currentPath));
const schema = computed(() => store.resolveSchema(immediateSchema.value));
const isContainer = computed(() => isContainerType(schema.value.type));
const triggers = computed(() => store.getTriggers(props.currentPath));
const isVisible = computed(() => store.isVisible(props.currentPath));
const isOverwritten = computed(() => store.isOverwritten(props.currentPath));
const issueFound = computed(() => hasIssues(props.currentPath));
const issue = computed(() => getIssue(props.currentPath));
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
        <DataMapperItem v-if="immediateSchema.type === 'DATA_MAPPER'" :currentPath="currentPath" :dynamicItemPath="dynamicItemPath" />
        <ValueSourceItem v-else-if="immediateSchema.type === 'VALUE_SOURCE'" :currentPath="currentPath" :dynamicItemPath="dynamicItemPath" />
        <ValueModifierItem v-else-if="immediateSchema.type === 'VALUE_MODIFIER'" :currentPath="currentPath" :dynamicItemPath="dynadynamicItemPathmicItem" />
        <ValueItem v-else-if="immediateSchema.type === 'VALUE'" :currentPath="currentPath" :dynamicItemPath="dynamicItemPath" />
        <RouteItem v-else-if="immediateSchema.type === 'ROUTE'" :currentPath="currentPath" :dynamicItemPath="dynamicItemPath" />
        <EvaluationItem v-else-if="immediateSchema.type === 'EVALUATION'" :currentPath="currentPath" :dynamicItemPath="dynamicItemPath" />
        <ComparisonItem v-else-if="immediateSchema.type === 'COMPARISON'" :currentPath="currentPath" :dynamicItemPath="dynamicItemPath" />
-->

        <ContainerItem v-if="schema.type === 'CONTAINER'"
                       :currentPath="currentPath"
                       :dynamicItemPath="dynamicItemPath" />
        <ListItem v-else-if="schema.type === 'LIST'"
                  :currentPath="currentPath"
                  :dynamicItemPath="dynamicItemPath" />
        <MapItem v-else-if="schema.type === 'MAP'"
                 :currentPath="currentPath"
                 :dynamicItemPath="dynamicItemPath" />
        <SwitchItem v-else-if="schema.type === 'SWITCH'"
                    :currentPath="currentPath"
                    :dynamicItemPath="dynamicItemPath" />
        <StringSwitchTypeItem v-else-if="schema.type === 'STRING' && triggers.indexOf('switch') >= 0"
                              :currentPath="currentPath" />
        <StringItem v-else-if="schema.type === 'STRING'"
                    :currentPath="currentPath"
                    :dynamicItemPath="dynamicItemPath" />
        <IntegerItem v-else-if="schema.type === 'INTEGER'"
                     :currentPath="currentPath"
                     :dynamicItemPath="dynamicItemPath" />
        <BooleanItem v-else-if="schema.type === 'BOOLEAN'"
                     :currentPath="currentPath"
                     :dynamicItemPath="dynamicItemPath" />

        <div v-if="issueFound">{{ issue }}</div>
    </div>
</template>

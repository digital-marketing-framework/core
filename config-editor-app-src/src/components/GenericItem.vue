<script setup>

import ContainerItem from './items/ContainerItem.vue';
import ListItem from './items/ListItem.vue';
import MapItem from './items/MapItem.vue';
import SwitchItem from './items/SwitchItem.vue';
import StringSwitchTypeItem from './items/StringSwitchTypeItem.vue';
import StringItem from './items/StringItem.vue';
import IntegerItem from './items/IntegerItem.vue';
import BooleanItem from './items/BooleanItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../stores/dmf';

const store = useDmfStore();

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    },
    isSwitchKey: {
        type: Boolean,
        required: false,
        default: () => false
    }
});

const item = computed(() => store.getItem(props.currentPath));
</script>
<template>
    <div class="w-full max-w-3xl"
         :class="{
             'bg-blue-100 text-blue-800 border border-blue-200 py-2 px-3 rounded': !item.isContainer
         }">

        <ContainerItem v-if="item.schema.type === 'CONTAINER'" :currentPath="currentPath" />
        <ListItem v-else-if="item.schema.type === 'LIST'" :currentPath="currentPath" />
        <MapItem v-else-if="item.schema.type === 'MAP'" :currentPath="currentPath" />
        <SwitchItem v-else-if="item.schema.type === 'SWITCH'" :currentPath="currentPath" />
        <StringSwitchTypeItem v-else-if="item.schema.type === 'STRING' && isSwitchKey" :currentPath="currentPath" />
        <StringItem v-else-if="item.schema.type === 'STRING'" :currentPath="currentPath"/>
        <IntegerItem v-else-if="item.schema.type === 'INTEGER'" :currentPath="currentPath"/>
        <BooleanItem v-else-if="item.schema.type === 'BOOLEAN'" :currentPath="currentPath" />

        <div v-if="item.hasIssues">{{ item.issue }}</div>
    </div>
</template>

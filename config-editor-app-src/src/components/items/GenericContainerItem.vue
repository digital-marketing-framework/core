<script setup>
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel
} from '@headlessui/vue';

import AngleDownIcon from '../icons/AngleDownIcon.vue';
import TransitionExpand from '../TransitionExpand.vue';
import ContainerHeader from './ContainerHeader.vue';
import RawItem from './RawItem.vue';

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
const containerState = computed(() => store.getContainerState(props.currentPath));
const skipHeader = computed(() => item.value.schema.skipHeader && !item.value.selected);
</script>

<template>
    <Disclosure v-if="!skipHeader" :default-open="containerState"
                    v-slot="{ open }">
        <ContainerHeader :currentPath="currentPath" :dynamicItem="dynamicItem">
            <template #disclosureButton>
                <DisclosureButton v-if="!item.selected" class="p-1" @click="store.toggleContainerState(currentPath)">
                    <AngleDownIcon class="w-3 h-3"
                                    :class="{
                                        '-rotate-90': !open
                                    }" />
                </DisclosureButton>
            </template>
        </ContainerHeader>
        <TransitionExpand>
            <DisclosurePanel>
                <div v-if="!item.rawView && item.childPaths.length > 0"
                    class="pt-3 pl-5 space-y-3">
                    <slot name="fieldsUi"></slot>
                </div>
                <RawItem v-else-if="item.rawView" :currentPath="currentPath"/>
            </DisclosurePanel>
        </TransitionExpand>
    </Disclosure>
    <div v-else class="space-y-3">
        <slot name="fieldsUi"></slot>
    </div>
</template>

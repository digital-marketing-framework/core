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

const schema = computed(() => store.getSchema(props.currentPath, undefined, true));
const containerState = computed(() => store.getContainerState(props.currentPath));
const selected = computed(() => store.isSelected(props.currentPath));
const skipHeader = computed(() => schema.value.skipHeader && !selected.value);
const childPaths = computed(() => store.getChildPaths(props.currentPath));
const rawView = computed(() => store.isRawView(props.currentPath));
</script>

<template>
    <Disclosure v-if="!skipHeader" :default-open="containerState"
                    v-slot="{ open }">
        <ContainerHeader :currentPath="currentPath" :dynamicItem="dynamicItem">
            <template #disclosureButton>
                <DisclosureButton v-if="!selected" class="p-1" @click="store.toggleContainerState(currentPath)">
                    <AngleDownIcon class="w-3 h-3"
                                    :class="{
                                        '-rotate-90': !open
                                    }" />
                </DisclosureButton>
            </template>
        </ContainerHeader>
        <TransitionExpand>
            <DisclosurePanel>
                <div v-if="!rawView && childPaths.length > 0"
                    class="pt-3 pl-5 space-y-3">
                    <slot name="fieldsUi"></slot>
                </div>
                <RawItem v-else-if="rawView" :currentPath="currentPath"/>
            </DisclosurePanel>
        </TransitionExpand>
    </Disclosure>
    <div v-else class="space-y-3">
        <slot name="fieldsUi"></slot>
    </div>
</template>

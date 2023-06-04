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
    }
});

const item = computed(() => store.getItem(props.currentPath));
const containerState = computed(() => store.getContainerState(props.currentPath));
</script>

<template>
    <Disclosure v-if="!item.schema.skipHeader" :default-open="containerState"
                    v-slot="{ open }">
        <ContainerHeader :currentPath="currentPath">
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
                <div v-if="!item.rawView">
                    <slot name="fieldsUi"></slot>
                </div>
                <RawItem v-else :currentPath="currentPath"/>
            </DisclosurePanel>
        </TransitionExpand>
    </Disclosure>
    <slot v-else name="fieldsUi"></slot>
</template>

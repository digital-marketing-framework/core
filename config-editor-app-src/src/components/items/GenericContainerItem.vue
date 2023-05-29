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
</script>

<template>
    <Disclosure default-open
                    v-slot="{ open }">
        <ContainerHeader :currentPath="currentPath">
            <template #disclosureButton>
                <DisclosureButton v-if="item.childPaths.length && !item.isRoot" class="p-1">
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
</template>

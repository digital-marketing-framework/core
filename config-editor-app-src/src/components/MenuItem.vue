<script setup>
import { computed } from "vue";

import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel
} from '@headlessui/vue';

import AngleDownIcon from './icons/AngleDownIcon.vue';
import ItemIcon from './icons/ItemIcon.vue';
import TransitionExpand from './TransitionExpand.vue';

import { useDmfStore } from '../stores/dmf';

const store = useDmfStore();
const props = defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

const item = computed(() => store.getItem(props.currentPath));
const containerNavigationState = computed(() => store.getContainerNavigationState(props.currentPath));
</script>

<template>
    <Disclosure :default-open="containerNavigationState" v-slot="{ open }">

        <div class="flex justify-between rounded cursor-pointer gap-x-3"
             :class="{
                 'hover:bg-indigo-100/80': !item.selected && item.isContainer,
                 'hover:bg-blue-100/80': !item.selected && !item.isContainer,
                 'bg-indigo-100/80': item.selected && item.isContainer,
                 'bg-blue-100/80': item.selected && !item.isContainer,
             }">
            <div class="flex group gap-x-2.5 items-center text-sm leading-6 font-semibold grow py-1.5 px-3"
                 :class="{
                     'text-gray-700': !item.selected,
                     'hover:text-indigo-600': item.isContainer,
                     'hover:text-blue-600': !item.isContainer,
                     'text-indigo-600': item.selected && item.isContainer,
                     'text-blue-600': item.selected && !item.isContainer,
                 }"
                 @click="store.selectPath(currentPath)">
                <ItemIcon :item-type="item.schema.type"
                          :active="item.selected" />
                {{ item.label }}
            </div>
            <DisclosureButton v-if="item.navigationChildPaths.length && !item.isRoot"
                              class="flex items-center justify-center w-8 hover:text-indigo-600"
                              @click="store.toggleContainerNavigationState(currentPath)">
                <AngleDownIcon class="w-3 h-3"
                               :class="open && 'rotate-180 transform'" />
            </DisclosureButton>
        </div>

        <TransitionExpand>
            <DisclosurePanel v-if="item.navigationChildPaths.length"
                             class="relative">
                <div role="list"
                     class="pl-6 space-y-1">

                    <MenuItem v-for="path in item.navigationChildPaths" :key="currentPath + '/' + path"
                        :currentPath="store.getAbsolutePath(path, currentPath)"/>

                </div>
            </DisclosurePanel>
        </TransitionExpand>
    </Disclosure>
</template>

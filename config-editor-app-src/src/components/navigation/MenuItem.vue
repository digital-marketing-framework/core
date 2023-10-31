<script setup>
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel
} from '@headlessui/vue';
import { computed } from "vue";
import { useDmfStore } from '../../stores/dmf';
import { useLabelProcessor } from '../../composables/label';
import { getAbsolutePath, isRoot as _isRoot } from '../../helpers/path';
import { isContainerType } from '../../helpers/type';

import AngleDownIcon from '../icons/AngleDownIcon.vue';
import ItemIcon from '../icons/ItemIcon.vue';
import TransitionExpand from '../TransitionExpand.vue';
import { useNavigation } from '../../composables/navigation';
import { usePathProcessor } from '../../composables/path';

const store = useDmfStore();
const { getLabel } = useLabelProcessor(store);
const { getNavigationChildPaths, getContainerNavigationState, toggleContainerNavigationState } = useNavigation(store);
const { isSelected, selectPath } = usePathProcessor(store);

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

const schema = computed(() => store.getSchema(props.currentPath, undefined, true));
const isContainer = computed(() => isContainerType(schema.value.type));
const isRoot = computed(() => _isRoot(props.currentPath));
const label = computed(() => getLabel(props.currentPath));
const selected = computed(() => isSelected(props.currentPath));
const containerNavigationState = computed(() => getContainerNavigationState(props.currentPath));
const navigationChildPaths = computed(() => getNavigationChildPaths(props.currentPath));
</script>

<template>
    <Disclosure :default-open="containerNavigationState" v-slot="{ open }">

        <div class="flex justify-between rounded cursor-pointer gap-x-3"
             :class="{
                 'hover:bg-indigo-100/80': !selected && isContainer,
                 'hover:bg-blue-100/80': !selected && !isContainer,
                 'bg-indigo-100/80': selected && isContainer,
                 'bg-blue-100/80': selected && !isContainer,
             }">
            <div class="flex group gap-x-2.5 items-center text-sm leading-6 font-semibold grow py-1.5 px-3"
                 :class="{
                     'text-gray-700': !selected,
                     'hover:text-indigo-600': isContainer,
                     'hover:text-blue-600': !isContainer,
                     'text-indigo-600': selected && isContainer,
                     'text-blue-600': selected && !isContainer,
                 }"
                 @click="selectPath(currentPath)">
                <ItemIcon :item-type="schema.type"
                          :active="selected" />
                {{ label }}
            </div>
            <DisclosureButton v-if="navigationChildPaths.length && !isRoot"
                              class="flex items-center justify-center w-8 hover:text-indigo-600"
                              @click="toggleContainerNavigationState(currentPath)">
                <AngleDownIcon class="w-3 h-3"
                               :class="open && 'rotate-180 transform'" />
            </DisclosureButton>
        </div>

        <TransitionExpand>
            <DisclosurePanel v-if="navigationChildPaths.length"
                             class="relative">
                <div role="list"
                     class="pl-6 space-y-1">

                    <MenuItem v-for="path in navigationChildPaths" :key="currentPath + '/' + path"
                        :currentPath="getAbsolutePath(path, currentPath)" />

                </div>
            </DisclosurePanel>
        </TransitionExpand>
    </Disclosure>
</template>

<script setup>
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel
} from '@headlessui/vue';
import { computed } from "vue";
import { useDmfStore } from '@/stores/dmf';
import { useLabelProcessor } from '@/composables/label';
import { getAbsolutePath, isRoot as _isRoot } from '@/helpers/path';
import { isContainerType } from '@/helpers/type';

import AngleDownIcon from '@/components/icons/AngleDownIcon.vue';
import ItemIcon from '@/components/icons/ItemIcon.vue';
import TransitionExpand from '@/components/TransitionExpand.vue';
import { useNavigation } from '@/composables/navigation';
import { usePathProcessor } from '@/composables/path';
import { useIconProcessor } from '@/composables/icon';

const store = useDmfStore();
const { getLabel } = useLabelProcessor(store);
const { getNavigationChildPaths, getContainerNavigationState, toggleContainerNavigationState } = useNavigation(store);
const { isSelected, selectPath } = usePathProcessor(store);
const { getIcon } = useIconProcessor(store);

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
const customIcon = computed(() => getIcon(props.currentPath, undefined, schema.value));
</script>

<template>
    <Disclosure :default-open="containerNavigationState" v-slot="{ open }">

        <div class="tw-flex tw-justify-between tw-rounded tw-cursor-pointer tw-gap-x-3"
             :class="{
                 'hover:tw-bg-accent-selected': !selected && isContainer,
                 'hover:tw-bg-field-selected': !selected && !isContainer,
                 'tw-bg-accent-selected': selected && isContainer,
                 'tw-bg-field-selected': selected && !isContainer,
             }">
            <div class="tw-flex tw-group tw-gap-x-2.5 tw-items-center tw-text-sm tw-leading-6 tw-font-semibold tw-grow tw-py-1.5 tw-px-3"
                 :class="{
                     'tw-text-muted': !selected,
                     'hover:tw-text-accent-hover': isContainer,
                     'hover:tw-text-field-accent': !isContainer,
                     'tw-text-accent-hover': selected && isContainer,
                     'tw-text-field-accent': selected && !isContainer,
                 }"
                 @click="selectPath(currentPath)">
                <ItemIcon :item-type="schema.type"
                          :custom-icon="customIcon"
                          :active="selected" />
                {{ label }}
            </div>
            <DisclosureButton v-if="navigationChildPaths.length && !isRoot"
                              class="tw-flex tw-items-center tw-justify-center tw-w-8 hover:tw-text-accent-hover"
                              @click="toggleContainerNavigationState(currentPath)">
                <AngleDownIcon class="tw-w-3 tw-h-3"
                               :class="open && 'tw-rotate-180 tw-transform'" />
            </DisclosureButton>
        </div>

        <TransitionExpand>
            <DisclosurePanel v-if="navigationChildPaths.length"
                             class="tw-relative">
                <div role="list"
                     class="tw-pl-6 tw-space-y-1">

                    <MenuItem v-for="path in navigationChildPaths" :key="currentPath + '/' + path"
                        :currentPath="getAbsolutePath(path, currentPath)" />

                </div>
            </DisclosurePanel>
        </TransitionExpand>
    </Disclosure>
</template>

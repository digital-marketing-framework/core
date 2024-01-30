<script setup>
import {
    Disclosure,
    DisclosureButton,
    DisclosurePanel
} from '@headlessui/vue';

import AngleDownIcon from '../../icons/AngleDownIcon.vue';
import TransitionExpand from '../../TransitionExpand.vue';
import GenericContainerHeader from './GenericContainerHeader.vue';
import RawItem from '../RawItem.vue';

import { computed } from "vue";
import { useDmfStore } from '../../../stores/dmf';
import { usePathProcessor } from '../../../composables/path';
import { useNavigation } from '../../../composables/navigation';
import { useRawProcessor } from '../../../composables/raw';

const store = useDmfStore();
const { getChildPaths } = usePathProcessor(store);
const { getContainerState, toggleContainerState } = useNavigation(store);
const { isRawView } = useRawProcessor(store);
const { isSelected } = usePathProcessor(store);

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

const schema = computed(() => store.getSchema(props.currentPath, undefined, true));
const containerState = computed(() => getContainerState(props.currentPath));
const selected = computed(() => isSelected(props.currentPath));
const skipHeader = computed(() => schema.value.skipHeader && !selected.value);
const childPaths = computed(() => getChildPaths(props.currentPath));
const rawView = computed(() => isRawView(props.currentPath));
</script>

<template>
    <Disclosure v-if="!skipHeader"
                :default-open="containerState"
                v-slot="{ open }">
        <GenericContainerHeader :currentPath="currentPath"
                                :dynamicItemPath="dynamicItemPath">
            <template #disclosureButton>
                <DisclosureButton v-if="!selected"
                                  class="tw-p-1"
                                  @click="toggleContainerState(currentPath)">
                    <AngleDownIcon class="tw-w-3 tw-h-3"
                                   :class="{
                                       '-tw-rotate-90': !open
                                   }" />
                </DisclosureButton>
            </template>
        </GenericContainerHeader>
        <TransitionExpand>
            <DisclosurePanel>
                <div v-if="!rawView && childPaths.length > 0"
                     class="tw-pt-3 tw-pl-5 tw-space-y-3">
                    <slot name="fieldsUi"></slot>
                </div>
                <RawItem v-else-if="rawView"
                         :currentPath="currentPath" />
            </DisclosurePanel>
        </TransitionExpand>
    </Disclosure>
    <div v-else
         class="tw-space-y-3">
        <slot name="fieldsUi"></slot>
    </div>
</template>

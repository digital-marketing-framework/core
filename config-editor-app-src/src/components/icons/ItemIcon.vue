<script setup>
import { computed } from 'vue';

import ContainerIcon from './ContainerIcon.vue';
import SwitchIcon from './SwitchIcon.vue';
import CustomIcon from './CustomIcon.vue';
import ListIcon from './ListIcon.vue';
import MapIcon from './MapIcon.vue';
import NumericInputIcon from './NumericInputIcon.vue';
import TextInputIcon from './TextInputIcon.vue';
import ToggleIcon from './ToggleIcon.vue';

import { isContainerType } from '../../helpers/type';

const props = defineProps({
    itemType: {
        type: String,
        required: true
    },
    active: {
        type: Boolean,
        required: false
    }
});

const iconClassName = computed(() => {
    return ["tw-h-4 tw-w-4 tw-shrink-0", {
        'tw-text-gray-400': !props.active,
        'group-hover:tw-text-indigo-600': isContainerType(props.itemType),
        'group-hover:tw-text-blue-600': !isContainerType(props.itemType),
        'tw-text-indigo-600 group-hover:tw-text-indigo-600': props.active && isContainerType(props.itemType),
        'tw-text-blue-600 group-hover:tw-text-blue-600': props.active && !isContainerType(props.itemType),
        '!tw-w-3.5 !tw-h-3.5': props.itemType === "CONTAINER" || props.itemType === "LIST",
    }];
});
</script>

<template>
    <TextInputIcon v-if="itemType === 'STRING'"
            :class="iconClassName" />
    <NumericInputIcon v-else-if="itemType === 'INTEGER'"
            :class="iconClassName" />
    <ToggleIcon v-else-if="itemType === 'BOOLEAN'"
            :class="iconClassName" />
    <ListIcon v-else-if="itemType === 'LIST'"
            :class="iconClassName" />
    <MapIcon v-else-if="itemType === 'MAP'"
            :class="iconClassName" />
    <ContainerIcon v-else-if="itemType === 'CONTAINER'"
            :class="iconClassName" />
    <SwitchIcon v-else-if="itemType === 'SWITCH'"
            :class="iconClassName" />
    <CustomIcon v-else
            :class="iconClassName" />
</template>

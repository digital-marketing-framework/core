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

import { isContainerType } from '@/helpers/type';

const props = defineProps({
    itemType: {
        type: String,
        required: true
    },
    customIcon: {
        type: String,
        required: false
    },
    active: {
        type: Boolean,
        required: false
    }
});

// TODO how to deal with custom icons?
//const type = computed(() => props.customIcon || props.itemType);
const type = computed(() => props.itemType);

const iconClassName = computed(() => {
    return ["tw-h-4 tw-w-4 tw-shrink-0", {
        'tw-text-muted': !props.active,
        'group-hover:tw-text-accent-hover': isContainerType(props.itemType),
        'group-hover:tw-text-field-accent': !isContainerType(props.itemType),
        'tw-text-accent-hover group-hover:tw-text-accent-hover': props.active && isContainerType(props.itemType),
        'tw-text-field-accent group-hover:tw-text-field-accent': props.active && !isContainerType(props.itemType),
        '!tw-w-3.5 !tw-h-3.5': props.itemType === "CONTAINER" || props.itemType === "LIST",
    }];
});
</script>

<template>
    <TextInputIcon v-if="type === 'STRING'"
            :class="iconClassName" />
    <NumericInputIcon v-else-if="type === 'INTEGER'"
            :class="iconClassName" />
    <ToggleIcon v-else-if="type === 'BOOLEAN'"
            :class="iconClassName" />
    <ListIcon v-else-if="type === 'LIST'"
            :class="iconClassName" />
    <MapIcon v-else-if="type === 'MAP'"
            :class="iconClassName" />
    <ContainerIcon v-else-if="type === 'CONTAINER'"
            :class="iconClassName" />
    <SwitchIcon v-else-if="type === 'SWITCH'"
            :class="iconClassName" />
    <CustomIcon v-else
            :class="iconClassName" />
</template>

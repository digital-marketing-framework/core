<script setup>
import {
  h,
  ref,
  watch
} from 'vue';

import { useTippy } from 'vue-tippy';
import { computed } from "vue";
import { useDmfStore } from '../../stores/dmf';

import DebugInfo from '../DebugInfo.vue';
import BugIcon from '../icons/BugIcon.vue';
import PlusIcon from '../icons/PlusIcon.vue';
import TrashIcon from '../icons/TrashIcon.vue';

const store = useDmfStore();

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    },
    dynamicItem: {
        type: String,
        required: false,
        default: null
    }
});

const schema = computed(() => store.getSchema(props.currentPath, undefined, true));
const isDynamic = computed(() => !store.settings.readonly && props.dynamicItem);
const resetOverwritePath = computed(() => isDynamic.value ? props.dynamicItem : props.currentPath);
const listSchema = computed(() => isDynamic.value ? store.getSchema('..', props.dynamicItem, true) : null);
const canMove = computed(() => isDynamic.value && listSchema.value.dynamicOrder);
const isDynamicContainer = computed(() => store.isDynamicContainerType(schema.value.type));
const canMoveUp = computed(() => canMove.value && !store.isFirstDynamicChild(props.dynamicItem));
const canMoveDown = computed(() => canMove.value && !store.isLastDynamicChild(props.dynamicItem));
const canResetOverwrite = computed(() => !store.settings.readonly && store.canResetOverwrite(isDynamic.value ? props.dynamicItem : props.currentPath));

const debugToggle = ref();
const { setContent } = useTippy(debugToggle, {
    content: h(DebugInfo, props),
    theme: 'light',
    delay: [0, null],
    allowHTML: true,
});

// Needed as VueTippy doesn't update content
watch(
    () => props.currentPath,
    () => {
        setContent(h(DebugInfo, props));
    }
);
</script>

<template>
    <div class="flex items-center gap-x-2">
        <div v-if="!store.settings.readonly && isDynamicContainer" @click="store.addValue(currentPath)" class="px-8 rounded">
            <PlusIcon class="w-3 h-3" />
        </div>
        <div v-if="isDynamic" @click="store.removeValue(dynamicItem)">
            <TrashIcon class="w-3 h-3" />
        </div>
        <div v-if="canMoveUp" @click="store.moveValueUp(dynamicItem)">
            <button type="button">up</button>
        </div>
        <div v-if="canMoveDown" @click="store.moveValueDown(dynamicItem)">
            <button type="button">down</button>
        </div>
        <div @click="store.toggleView(currentPath)" class="p-1 text-indigo-400">
            <button type="button">&lt;/&gt;</button>
        </div>
        <div v-if="canResetOverwrite" @click="store.resetValue(resetOverwritePath)" class="p-1 text-indigo-400">
            <button type="button">&lt;&lt;</button>
        </div>
        <div ref="debugToggle" class="p-1 text-indigo-400 hover:text-indigo-500">
            <BugIcon class="w-3 h-3" />
        </div>
    </div>
</template>

<script setup>
import {
  h,
  ref,
  watch
} from 'vue';

import { useTippy } from 'vue-tippy';

import DebugInfo from '../DebugInfo.vue';
import BugIcon from '../icons/BugIcon.vue';

import PlusIcon from '../icons/PlusIcon.vue';
import TrashIcon from '../icons/TrashIcon.vue';

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

const item = computed(() => store.getItem(props.currentPath));

const isDynamic = computed(() => !store.settings.readonly && props.dynamicItem);
const listItem = computed(() => isDynamic.value ? store.getParentItem(props.dynamicItem.path) : null);
const canMove = computed(() => isDynamic.value && listItem.value.schema.dynamicOrder);
const canMoveUp = computed(() => canMove.value && !store.isFirstDynamicChild(props.dynamicItem.path));
const canMoveDown = computed(() => canMove.value && !store.isLastDynamicChild(props.dynamicItem.path));

const canResetOverwrite = computed(() => !store.settings.readonly && (isDynamic.value ? store.canResetOverwrite(props.dynamicItem.path) : item.value.canResetOverwrite));
const resetOverwritePath = computed(() => isDynamic.value ? props.dynamicItem.path : item.value.path);

const debugToggle = ref();
const { setContent } = useTippy(debugToggle, {
    content: h(DebugInfo, props),
    theme: 'light',
    delay: [0, null],
    allowHTML: true,
});

// Needed as VueTippy doesn't update content
watch(() => item, () => {
    setContent(h(DebugInfo, props));
});
</script>

<template>
    <div class="flex items-center gap-x-2">
        <div v-if="!store.settings.readonly && item.isDynamicContainer" @click="store.addValue(currentPath)" class="px-8 rounded">
            <PlusIcon class="w-3 h-3" />
        </div>
        <div v-if="isDynamic" @click="store.removeValue(dynamicItem.path)">
            <TrashIcon class="w-3 h-3" />
        </div>
        <div v-if="canMoveUp" @click="store.moveValueUp(dynamicItem.path)">
            <button type="button">up</button>
        </div>
        <div v-if="canMoveDown" @click="store.moveValueDown(dynamicItem.path)">
            <button type="button">down</button>
        </div>
        <div @click="store.toggleView(currentPath)" class="p-1 text-indigo-400">
            <button type="button">&lt;/&gt;</button>
        </div>
        <div v-if="canResetOverwrite" @click="store.resetValue(resetOverwritePath)" class="p-1 text-indigo-400">
            <button type="button">&lt;&lt;</button>
        </div>
        <div ref="debugToggle"
                class="p-1 text-indigo-400 hover:text-indigo-500">
            <BugIcon class="w-3 h-3" />
        </div>
    </div>
</template>

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
    isSwitchKey: {
        type: Boolean,
        required: false,
        default: () => false
    }
});

const item = computed(() => store.getItem(props.currentPath));

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
        <div v-if="item.isDynamicContainer" v-on:click="store.addValue(currentPath)" class="px-8 rounded">
            <PlusIcon class="w-3 h-3" />
        </div>
        <div v-if="item.isDynamicItem" v-on:click="store.removeValue(currentPath)">
            <TrashIcon class="w-3 h-3" />
        </div>
        <div @click="store.toggleView(currentPath)" class="p-1 text-indigo-400">
            <button type="button">&lt;/&gt;</button>
        </div>
        <div v-if="item.isOverwritten" v-on:click="store.resetValue(currentPath)" class="p-1 text-indigo-400">
            <button type="button">&lt;&lt;</button>
        </div>
        <div ref="debugToggle"
                class="p-1 text-indigo-400 hover:text-indigo-500">
            <BugIcon class="w-3 h-3" />
        </div>
    </div>
</template>

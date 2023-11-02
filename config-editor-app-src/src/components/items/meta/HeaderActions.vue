<script setup>
import {
    h,
    ref,
    watch
} from 'vue';

import { useTippy } from 'vue-tippy';
import { computed } from "vue";
import { useDmfStore } from '../../../stores/dmf';
import { isContainerType, isDynamicContainerType } from '../../../helpers/type';
import { useDynamicProcessor } from '../../../composables/dynamicItem';
import { useRawProcessor } from '../../../composables/raw';
import { useConfirmation } from '../../../composables/confirmation';

import BugIcon from '../../icons/BugIcon.vue';
import CodeIcon from '../../icons/CodeIcon.vue';
import CopyIcon from '../../icons/CopyIcon.vue';
import DebugInfo from './DebugInfo.vue';
import PlusIcon from '../../icons/PlusIcon.vue';
import RotateLeftIcon from '../../icons/RotateLeftIcon.vue';
import SortDownIcon from '../../icons/SortDownIcon.vue';
import SortUpIcon from '../../icons/SortUpIcon.vue';
import TrashIcon from '../../icons/TrashIcon.vue';

const store = useDmfStore();
const {
    isFirstChild,
    isLastChild,
    moveValueUp,
    moveValueDown,
    addValue,
    removeValue,
    copyValue
} = useDynamicProcessor(store);
const { toggleRawView, isRawView } = useRawProcessor(store);
const { requestConfirmation } = useConfirmation(store);

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
const raw = computed(() => isRawView(props.currentPath));
const isDynamic = computed(() => !store.settings.readonly && props.dynamicItemPath);
const resetOverwritePath = computed(() => isDynamic.value ? props.dynamicItemPath : props.currentPath);
const listSchema = computed(() => isDynamic.value ? store.getSchema('..', props.dynamicItemPath, true) : null);
const canMove = computed(() => isDynamic.value && listSchema.value.dynamicOrder);
const isDynamicContainer = computed(() => isDynamicContainerType(schema.value.type));
const canMoveUp = computed(() => canMove.value && !isFirstChild(props.dynamicItemPath));
const canMoveDown = computed(() => canMove.value && !isLastChild(props.dynamicItemPath));
const canResetOverwrite = computed(() => !store.settings.readonly && store.canResetOverwrite(isDynamic.value ? props.dynamicItemPath : props.currentPath));

const reset = () => {
    requestConfirmation(
        (answer) => {
            if (answer) {
                store.resetValue(resetOverwritePath.value);
            }
        },
        'Are you sure you want to reset this part of the configuration?',
        'The value'
        + (isContainerType(schema.value.type) ? ' (and all sub values)' : '')
        + ' will be reset to the inherited value from parent document(s). If there is no inherited value, the default value will be used.'
    );
};

const remove = () => {
    requestConfirmation(
        (answer) => {
            if (answer) {
                removeValue(props.dynamicItemPath);
            }
        },
        'Are you sure you want to delete this item?',
        'Just like the spoon, there is no undo button.'
    );
};

const debug = computed(() => store.settings.debug);
const debugToggle = ref();
const { setContent } = useTippy(debugToggle, {
    content: h(DebugInfo, props),
    theme: 'light',
    delay: [0, null],
    allowHTML: true,
});

// Needed as VueTippy doesn't update content
watch(
    () => debug.value && props.currentPath,
    () => {
        if (debug.value) {
            setContent(h(DebugInfo, props));
        }
    }
);
</script>

<template>
    <div class="flex items-center gap-x-2">
        <div v-if="canResetOverwrite"
             @click="reset()"
             class="p-1 text-indigo-400 hover:text-indigo-500">
            <RotateLeftIcon class="w-3 h-3" />
        </div>
        <div v-if="isDynamic"
             @click="copyValue(dynamicItemPath)"
             class="p-1 text-indigo-400 hover:text-indigo-500">
            <CopyIcon class="w-3 h-3" />
        </div>
        <div v-if="canMove"
             @click="canMoveUp && moveValueUp(dynamicItemPath)"
             class="p-1"
             :class="{
                 'text-indigo-500': canMoveUp,
                 'text-indigo-400': !canMoveUp
             }">
            <SortUpIcon class="w-3 h-3" />
        </div>
        <div v-if="canMove"
             @click="canMoveDown && moveValueDown(dynamicItemPath)"
             class="p-1"
             :class="{
                 'text-indigo-500': canMoveDown,
                 'text-indigo-400': !canMoveDown
             }">
            <SortDownIcon class="w-3 h-3" />
        </div>
        <div v-if="isDynamic"
             @click="remove()"
             class="p-1 text-indigo-400 hover:text-indigo-500">
            <TrashIcon class="w-3 h-3" />
        </div>
        <div v-if="!store.settings.readonly && isDynamicContainer"
             @click="addValue(currentPath)"
             class="p-1 text-indigo-400 hover:text-indigo-500">
            <PlusIcon class="w-3 h-3" />
        </div>
        <div @click="toggleRawView(currentPath)"
             class="p-1"
             :class="{
                 'text-indigo-500': raw,
                 'text-indigo-400': !raw
             }">
            <CodeIcon class="w-4 h-4" />
        </div>
        <div v-if="debug"
             ref="debugToggle"
             class="p-1 text-indigo-400 hover:text-indigo-500">
            <BugIcon class="w-3 h-3" />
        </div>
    </div>
</template>

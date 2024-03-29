<script setup>
import { computed } from "vue";
import { useDmfStore } from '../../stores/dmf';
import { useRawProcessor } from "../../composables/raw";

import { getPrismHighlighter } from '../../helpers/rawValue';

import 'vue-prism-editor/dist/prismeditor.min.css'; // import the styles somewhere
import 'prismjs/themes/prism-funky.css';

import { PrismEditor } from 'vue-prism-editor';
import UuidGenerator from "./meta/UuidGenerator.vue";

const store = useDmfStore();
const { getRawValue, getRawIssue, setRawValue } = useRawProcessor(store);

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

const highlighter = getPrismHighlighter(store.settings.rawLanguage);

const rawValue = computed({
    get() {
        let rawData = store.rawValues[props.currentPath];
        if (typeof rawData !== 'undefined') {
            return rawData;
        }
        return getRawValue(props.currentPath);
    },
    set(rawData) {
        store.rawValues[props.currentPath] = rawData;
        setRawValue('.', props.currentPath, rawData);
    }
});

const rawIssue = computed(() => getRawIssue(props.currentPath));
</script>
<template>
    <PrismEditor class="tw-flex-1 tw-block tw-w-full tw-p-4 tw-overflow-y-auto tw-font-mono tw-text-sm tw-whitespace-pre-wrap tw-bg-indigo-900 tw-overscroll-none"
                 v-model="rawValue"
                 :readonly="store.settings.readonly"
                 :highlight="highlighter"
                 :line-numbers="true" />
    <div v-if="rawIssue">{{ rawIssue }}</div>
    <UuidGenerator />
</template>

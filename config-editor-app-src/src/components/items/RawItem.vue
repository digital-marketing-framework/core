<script setup>
import { computed } from "vue";
import { useDmfStore } from '../../stores/dmf';

import { getPrismHighlighter } from '../../composables/rawValueHelper';

import 'vue-prism-editor/dist/prismeditor.min.css'; // import the styles somewhere
import 'prismjs/themes/prism-funky.css';

import { PrismEditor } from 'vue-prism-editor';

const store = useDmfStore();

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
    return store.getRawValue(props.currentPath);
  },
  set(rawData) {
    store.rawValues[props.currentPath] = rawData;
    store.setRawValue('.', props.currentPath, rawData);
  }
});

const rawIssue = computed(() => store.getRawIssue(props.currentPath));
</script>
<template>
    <PrismEditor
            class="flex-1 block w-full p-4 overflow-y-auto font-mono text-sm whitespace-pre-wrap bg-indigo-900 overscroll-none"
            v-model="rawValue"
            :readonly="store.settings.readonly"
            :highlight="highlighter"
            line-numbers></PrismEditor>
    <div v-if="rawIssue">{{ rawIssue }}</div>
</template>

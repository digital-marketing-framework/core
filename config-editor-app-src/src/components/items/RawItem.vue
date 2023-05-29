<script setup>
import { computed } from "vue";
import { useDmfStore } from '../../stores/dmf';

import 'vue-prism-editor/dist/prismeditor.min.css'; // import the styles somewhere
import 'prismjs/themes/prism-funky.css';

import YAML from 'yaml';

// import highlighting library (you can use any library you want just return html string)
import prism from 'prismjs';
import { PrismEditor } from 'vue-prism-editor'; //
import "prismjs/components/prism-yaml";

const store = useDmfStore();

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

const rawLanguage = store.settings.rawLanguage;

const highlighter = (code) => {
    let langKey;
    switch (rawLanguage) {
        case 'YAML': {
            langKey = 'yaml';
            break;
        }
        case 'JSON': {
            langKey = 'js';
            break;
        }
        default: {
            throw new Error('unkdnown raw code language: ' + rawLanguage);
        }
    }
    return prism.highlight(code, prism.languages[langKey]);
};

const rawValue = computed({
  get() {
    const data = store.getValue('.', props.currentPath, true);
    let dataAsString;
    switch (rawLanguage) {
        case 'YAML': {
            dataAsString = YAML.stringify(data);
            break;
        }
        case 'JSON': {
            dataAsString = JSON.stringify(data, null, 2);
            break;
        }
        default: {
            throw new Error('unknown raw code language: ' + rawLanguage);
        }
    }
    return dataAsString;
  },
  set(value) {
    try {
        let dataFromString;
        switch (rawLanguage) {
            case 'YAML': {
                dataFromString = YAML.parse(value);
                break;
            }
            case 'JSON': {
                dataFromString = JSON.parse(value);
                break;
            }
            default: {
                throw new Error('unknown raw code language: ' + rawLanguage);
            }
        }
        store.setValue('.', props.currentPath, dataFromString);
    } catch (e) {
        console.warn('could not parse raw code', rawLanguage, e);
    }
  }
});
</script>
<template>
    <PrismEditor
            class="flex-1 block w-full p-4 overflow-y-auto font-mono text-sm whitespace-pre-wrap bg-indigo-900 overscroll-none"
            v-model="rawValue"
            :highlight="highlighter"
            line-numbers></PrismEditor>
</template>

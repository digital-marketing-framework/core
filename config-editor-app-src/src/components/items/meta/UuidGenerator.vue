<script setup>
import { ref, nextTick } from "vue";
import RotateLeftIcon from "../../icons/RotateLeftIcon.vue";
import CopyIcon from "../../icons/CopyIcon.vue";
import CopySolidIcon from "../../icons/CopySolidIcon.vue";

import { v4 as uuidv4 } from 'uuid';

const newUuidCopied = ref(false);
const newUuidInput = ref(null);
const newUuid = ref(uuidv4());

const updateUuid = () => {
    newUuid.value = uuidv4();
    newUuidCopied.value = false;
    // copyUuid(); // TODO should we copy the uuid as soon as we generated one?
    focusUuid();
};

const copyUuid = async () => {
    try {
        await navigator.clipboard.writeText(newUuid.value);
        newUuidCopied.value = true;
    } catch (e) {
        // copying not possible
    }
    focusUuid();
};

const focusUuid = async () => {
    newUuidInput.value.focus();
    await nextTick();
    selectUuid();
};

const selectUuid = () => {
    newUuidInput.value.select();
};

</script>
<template>
    <div class="w-full max-w-3xl bg-blue-100 text-blue-800 border border-blue-200 py-2 px-3 rounded">
        <header class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-x-1">
                <label :for="'input_raw_uuid' + currentPath"
                       class="font-medium text-sm">UUID Generator</label>
            </div>
            <div class="flex items-center gap-x-2">
                <div @click="updateUuid()"
                     class="p-1 text-indigo-400 hover:text-indigo-500">
                    <RotateLeftIcon class="w-3 h-3" />
                </div>
                <div @click="copyUuid()"
                     class="p-1 text-indigo-400 hover:text-indigo-500">
                    <CopySolidIcon v-if="newUuidCopied"
                                   class="w-3 h-3" />
                    <CopyIcon v-else
                              class="w-3 h-3" />
                </div>
            </div>
        </header>
        <div class="mt-2">
            <input :id="'input_raw_uuid' + currentPath"
                   :name="'input_raw_uuid' + currentPath"
                   v-model="newUuid"
                   type="text"
                   autocomplete="off"
                   @focus="selectUuid"
                   ref="newUuidInput"
                   class="block w-full rounded border-0 py-1.5 text-gray-900 placeholder:text-blue-800 placeholder:opacity-60 shadow-sm ring-1 ring-inset ring-blue-200 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                   readonly="readonly" />
        </div>
    </div>
</template>

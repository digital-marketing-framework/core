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
    <div class="tw-w-full tw-max-w-3xl tw-bg-blue-100 tw-text-blue-800 tw-border tw-border-blue-200 tw-py-2 tw-px-3 tw-rounded">
        <header class="tw-flex tw-items-center tw-justify-between tw-gap-4">
            <div class="tw-flex tw-items-center tw-gap-x-1">
                <label :for="'input_raw_uuid' + currentPath"
                       class="tw-font-medium tw-text-sm">UUID Generator</label>
            </div>
            <div class="tw-flex tw-items-center tw-gap-x-2">
                <div @click="updateUuid()"
                     class="tw-p-1 tw-text-indigo-400 hover:tw-text-indigo-500">
                    <RotateLeftIcon class="tw-w-3 tw-h-3" />
                </div>
                <div @click="copyUuid()"
                     class="tw-p-1 tw-text-indigo-400 hover:tw-text-indigo-500">
                    <CopySolidIcon v-if="newUuidCopied"
                                   class="tw-w-3 tw-h-3" />
                    <CopyIcon v-else
                              class="tw-w-3 tw-h-3" />
                </div>
            </div>
        </header>
        <div class="tw-mt-2">
            <input :id="'input_raw_uuid' + currentPath"
                   :name="'input_raw_uuid' + currentPath"
                   v-model="newUuid"
                   type="text"
                   autocomplete="off"
                   @focus="selectUuid"
                   ref="newUuidInput"
                   class="tw-form-input tw-block tw-w-full tw-rounded tw-border-0 tw-py-1.5 tw-text-gray-900 placeholder:tw-text-blue-800 placeholder:tw-opacity-60 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-blue-200 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-blue-600 sm:tw-text-sm sm:tw-leading-6"
                   readonly="readonly" />
        </div>
    </div>
</template>

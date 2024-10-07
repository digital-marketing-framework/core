<script setup>
import { computed } from "vue";
import { useDmfStore } from '@/stores/dmf';
import { isContainerType, isDynamicContainerType } from '@/helpers/type';
import { useDynamicProcessor } from '@/composables/dynamicItem';
import { useRawProcessor } from '@/composables/raw';
import { useConfirmation } from '@/composables/confirmation';

import RotateLeftIcon from '../../icons/RotateLeftIcon.vue';

const store = useDmfStore();
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
const isDynamic = computed(() => !store.settings.readonly && props.dynamicItemPath);
const resetOverwritePath = computed(() => isDynamic.value ? props.dynamicItemPath : props.currentPath);
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

</script>

<template>
    <div class="custom-class-overwritten">
        <div v-if="canResetOverwrite"
                @click="reset()"
                class="resetOverwrite tw-p-1 tw-text-indigo-400 hover:tw-text-indigo-500">
            <RotateLeftIcon class="tw-w-3 tw-h-3" />
        </div>
    </div>
</template>

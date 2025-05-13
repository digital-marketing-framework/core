<script setup>
import { computed } from "vue";
import { useDmfStore } from '@/stores/dmf';
import { isContainerType, isDynamicContainerType, isScalarType } from '@/helpers/type';
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
const isScalar = computed(() => isScalarType(schema.value.type));
const isContainer = computed(() => isContainerType(schema.value.type));
const isDynamicContainer = computed(() => isDynamicContainerType(schema.value.type));

const reset = () => {
    requestConfirmation(
        (answer) => {
            if (answer) {
                store.resetValue(resetOverwritePath.value);
            }
        },
        'Are you sure you want to reset this part of the configuration?',
        'The value'
        + (isContainer.value ? ' (and all sub values)' : '')
        + ' will be reset to the inherited value from parent document(s). If there is no inherited value, the default value will be used.'
    );
};

</script>

<template>
    <div class="custom-class-overwritten"
        :class="{
            'item-type-container': isContainer,
            'item-type-dynamic-container': isDynamicContainer,
            'item-type-scalar': isScalar
        }"
        >
        <div v-if="canResetOverwrite"
                @click="reset()"
                class="resetOverwrite tw-p-1 tw-text-indigo-400 hover:tw-text-indigo-500">
            <RotateLeftIcon class="tw-w-3 tw-h-3" />
        </div>
    </div>
</template>

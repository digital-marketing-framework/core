<script setup>

import GenericItem from '../../GenericItem.vue';
import { getAbsolutePath } from '../../../../helpers/path';

defineProps({
    currentPath: {
        type: String,
        required: true
    }
});

</script>

<template>
    <GenericItem :currentPath="getAbsolutePath('value', currentPath)"
                 :dynamicItemPath="currentPath" />
</template>

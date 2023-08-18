<script setup>
// import { storeToRefs } from 'pinia';
import { computed } from "vue";
import { useDmfStore } from '../stores/dmf';

const store = useDmfStore();
// const { getItem } = storeToRefs(store);

const props = defineProps({
    currentPath: {
        type: String,
        required: true
    },
    dynamicItem: {
        type: Object,
        required: false,
        default: null
    }
});

const item = computed(() => store.getItem(props.currentPath));
</script>

<template>
    <div class="text-xs text-left text-gray-500">
        <table>
            <tr>
                <th class="p-1 align-top">Path</th>
                <td class="p-1 align-top"><span class="break-all">{{ item.path }}</span></td>
            </tr>
            <tr>
                <th class="p-1 align-top">Label</th>
                <td class="p-1 align-top">{{ item.label }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Type</th>
                <td class="p-1 align-top">{{ item.schema.type }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Level</th>
                <td class="p-1 align-top">{{ item.level }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Selected</th>
                <td class="p-1 align-top">{{ item.selected }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Overwritten</th>
                <td class="p-1 align-top">{{ item.isOverwritten }}</td>
            </tr>
            <tr v-if="item.isScalar">
                <th class="p-1 align-top">Value</th>
                <td class="p-1 align-top"><span class="break-all">{{ item.value }}</span></td>
            </tr>
            <tr v-if="item.isContainer">
                <th class="p-1 align-top">Dynamic Container</th>
                <td class="p-1 align-top">{{ item.isDynamicContainer }}</td>
            </tr>
            <tr>
                <th class="p-1 align-top">Dynamic Item</th>
                <td class="p-1 align-top" >{{ !!dynamicItem }}</td>
            </tr>
            <tr v-if="store.isCustomType(store.getSchema(item.path).type)">
                <th class="p-1 align-top">Immediate Schema</th>
                <td class="p-1 align-top">{{ store.getSchema(item.path) }}</td>
            </tr>
            <tr v-if="item.isScalar">
                <th class="p-1 align-top">Schema</th>
                <td class="p-1 align-top">{{ item.schema }}</td>
            </tr>
            <tr v-if="item.triggers.length > 0">
                <th class="p-1 align-top">Triggers</th>
                <td class="p1-align-top">{{ item.triggers }}</td>
            </tr>
        </table>
    </div>
</template>

<script setup>

import { computed, onMounted, watch } from "vue";
import { useDmfStore } from './stores/dmf';

import ArrowLeftLongIcon from './components/icons/ArrowLeftLongIcon.vue';
import MediatisLogo from './components/icons/MediatisLogo.vue';

import MenuItem from './components/MenuItem.vue';
import GenericItem from './components/GenericItem.vue';

const store = useDmfStore();

const selectedPath = computed(() => store.getSelectedPath());
const rootSelected = computed(() => store.isRoot(selectedPath.value));

onMounted(() => {
  store.fix('/');
  if (!window.DMF_CONFIG_EDITOR) {
    store.fetchData();
  }
});

watch(() => selectedPath, () => {
  this.$forceUpdate();
});
</script>

<template>
  <main class="flex flex-col min-h-screen">
    <div class="flex grow">
      <div class="sticky top-0 z-50 flex h-screen shrink-0 w-96">
        <div class="flex flex-col w-full overflow-y-auto bg-white border-r border-gray-200 grow gap-y-5 overscroll-none">

          <div class="sticky top-0 z-10 flex items-baseline gap-3 px-6 py-6 shrink-0 bg-white/50 backdrop-blur-sm">
            <MediatisLogo class="h-8"></MediatisLogo>
            <span class="text-2xl leading-none font-caveat">dmf</span>
          </div>

          <nav class="px-6 space-y-1 grow">
            <MenuItem currentPath="/" />
          </nav>

          <div class="sticky bottom-0 px-6 py-3 text-2xl font-caveat bg-white/50 backdrop-blur-sm">
            <a href="https://www.mediatis.de"
              class="flex items-center leading-6 text-gray-900 gap-x-4 hover:bg-gray-50">
              <span aria-hidden="true">Mediatis AG</span>
            </a>
          </div>

        </div>
      </div>

      <div class="flex grow">
        <div class="grow">
          <div class="sticky top-0 z-10 px-4 py-3 text-right bg-gray-100 border-b border-gray-200 sm:px-6">
            <button type="button"
                    @click="store.save()"
                    class="rounded px-4 text-sm py-1.5 disabled:opacity-50 bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
              Save
            </button>
            <button type="button"
                    @click="store.updateIncludes()"
                    class="rounded px-4 text-sm py-1.5 disabled:opacity-50 bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
              Update includes
            </button>
          </div>
          <!--
          <ul>
            <li v-for="(message, index) in store.messages" :key="index" :class="message.type">
                {{ message.text }}
                <button @click="store.messages.splice(index, 1)">X</button>
            </li>
          </ul>
          -->
          <div class="flex justify-center px-4 py-10 sm:px-6 grow">
            <div class="grow max-w-7xl">
              <button type="button" v-if="!rootSelected"
                      class="flex items-center mb-4 text-xs text-gray-500 hover:text-gray-600 gap-x-2">
                <ArrowLeftLongIcon class="w-3 h-3 shrink-0" @click="store.selectParentPath()" />
                <span v-for="rootLinePath in store.getRootLine(selectedPath)" :key="rootLinePath">
                  / <span @click="store.selectPath(rootLinePath)">{{ store.getLabel(rootLinePath) }}</span>
                </span>
              </button>
              <GenericItem :currentPath="selectedPath" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

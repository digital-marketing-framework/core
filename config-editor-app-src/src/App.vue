<script setup>
import { computed } from "vue";
import { useDmfStore } from './stores/dmf';
import { isRoot, getRootLine } from './helpers/path';
import { useLabelProcessor } from './composables/label';
import { usePathProcessor } from "./composables/path";
import { useDocument } from "./composables/document";

import ArrowLeftLongIcon from './components/icons/ArrowLeftLongIcon.vue';
import MediatisLogo from './components/icons/MediatisLogo.vue';
import TimedMessage from './components/TimedMessage.vue';
import ConfirmationDialog from "./components/ConfirmationDialog.vue";
import MenuItem from './components/navigation/MenuItem.vue';
import GenericItem from './components/items/GenericItem.vue';

const store = useDmfStore();
const { getLabel } = useLabelProcessor(store);
const { getSelectedPath, selectPath } = usePathProcessor(store);
const { getDocumentName } = useDocument(store);

const selectedPath = computed(() => getSelectedPath());
const rootSelected = computed(() => isRoot(selectedPath.value));
const rootLine = computed(() => getRootLine(selectedPath.value));

const warnings = computed(() => {
    const warnings = [];
    Object.keys(store.warnings).forEach(key => {
        warnings.push(store.warnings[key]);
    });
    return warnings;
});

const documentName = computed(() => getDocumentName());
const showApp = computed(() => store.loaded && store.isOpen);
const confirmationDialogOpen = computed(() => store.confirmDialog.open);
</script>

<template>
    <main class="tw-absolute tw-flex tw-flex-col tw-overflow-hidden tw-bg-white"
          :class="{
              'tw-inset-4 tw-rounded': store.settings.mode === 'modal',
              'tw-inset-0': store.settings.mode === 'embedded',
          }"
          v-if="showApp">
        <div class="tw-flex tw-grow tw-h-full">
            <div class="tw-relative tw-z-50 tw-flex tw-shrink-0 tw-w-96">
                <div
                     class="tw-flex tw-flex-col tw-w-full tw-overflow-y-auto tw-bg-white tw-border-r tw-border-gray-200 tw-grow tw-gap-y-5 tw-overscroll-none">

                    <div
                         class="tw-sticky tw-top-0 tw-z-10 tw-flex tw-items-baseline tw-gap-3 tw-px-6 tw-py-6 tw-shrink-0 tw-bg-white/50 tw-backdrop-blur-sm">
                        <MediatisLogo class="tw-h-8"></MediatisLogo>
                        <span class="tw-text-2xl tw-leading-none tw-font-caveat">dmf</span>
                    </div>

                    <nav class="tw-px-6 tw-space-y-1 tw-grow">
                        <MenuItem currentPath="/" />
                    </nav>

                    <div class="tw-sticky tw-bottom-0 tw-px-6 tw-py-3 tw-text-2xl tw-font-caveat tw-bg-white/50 tw-backdrop-blur-sm">
                        <a href="https://www.mediatis.de"
                           class="tw-flex tw-items-center tw-leading-6 tw-text-gray-900 tw-gap-x-4 hover:tw-bg-gray-50"
                           target="_blank">
                            <span aria-hidden="true">Mediatis AG</span>
                        </a>
                    </div>

                </div>
            </div>

            <div class="tw-flex tw-grow tw-overflow-auto">
                <div class="tw-grow">
                    <div class="tw-sticky tw-top-0 tw-z-10 tw-px-4 tw-py-3 tw-text-right tw-bg-gray-100 tw-border-b tw-border-gray-200 sm:tw-px-6 tw-flex tw-items-center">
                        <span class="tw-text-2xl tw-leading-none tw-font-caveat">{{ documentName }}</span>
                        <div class="tw-space-x-4 tw-ml-auto tw-pl-4">
                            <button type="button"
                                    @click="store.close()"
                                    class="tw-rounded tw-px-4 tw-text-sm tw-py-1.5 disabled:tw-opacity-50 tw-bg-blue-600 tw-font-semibold tw-text-white tw-shadow-sm hover:tw-bg-blue-500 focus-visible:tw-outline focus-visible:tw-outline-2 focus-visible:tw-outline-offset-2 focus-visible:tw-outline-blue-600">
                                <span v-if="store.settings.mode === 'embedded'">Close</span>
                                <span v-else-if="store.settings.mode === 'modal'">Discard</span>
                            </button>
                            <button type="button"
                                    @click="store.save()"
                                    class="tw-rounded tw-px-4 tw-text-sm tw-py-1.5 disabled:tw-opacity-50 tw-bg-blue-600 tw-font-semibold tw-text-white tw-shadow-sm hover:tw-bg-blue-500 focus-visible:tw-outline focus-visible:tw-outline-2 focus-visible:tw-outline-offset-2 focus-visible:tw-outline-blue-600">
                                <span v-if="store.settings.mode === 'embedded'">Save</span>
                                <span v-else-if="store.settings.mode === 'modal'">Confirm</span>
                            </button>
                        </div>
                    </div>

                    <ul class="tw-p-4 sm:tw-px-6"
                        v-if="store.messages.length > 0">
                        <TimedMessage v-for="(message, index) in store.messages"
                                      :key="index"
                                      :index="index" />
                    </ul>

                    <ul class="tw-p-4 sm:tw-px-6"
                        v-if="warnings.length > 0">
                        <li class="tw-border tw-border-red-700 tw-rounded tw-px-4 tw-py-3 tw-w-full tw-max-w-3xl tw-mb-3"
                            v-for="(warning, index) in warnings"
                            :key="index">
                            <button type="button"
                                    class="tw-rounded tw-px-4 tw-text-sm tw-py-1.5 disabled:tw-opacity-50 tw-bg-blue-600 tw-font-semibold tw-text-white tw-shadow-sm hover:tw-bg-blue-500 focus-visible:tw-outline focus-visible:tw-outline-2 focus-visible:tw-outline-offset-2 focus-visible:tw-outline-blue-600"
                                    v-if="warning.action && !warning.actionLabel"
                                    @click="warning.action()">
                                    {{ warning.message }}
                            </button>
                            <span class="tw-flex tw-items-center tw-justify-between"
                                v-else>
                                <span class="tw-text-sm tw-font-semibold">{{ warning.message }}</span>
                                <span v-if="warning.actionLabel">
                                    <button type="button"
                                        class="tw-rounded tw-px-4 tw-text-sm tw-py-1.5 disabled:tw-opacity-50 tw-bg-blue-600 tw-font-semibold tw-text-white tw-shadow-sm hover:tw-bg-blue-500 focus-visible:tw-outline focus-visible:tw-outline-2 focus-visible:tw-outline-offset-2 focus-visible:tw-outline-blue-600"
                                        v-if="warning.action"
                                        @click="warning.action()">{{ warning.actionLabel }}
                                    </button>
                                    <span v-else>{{ warning.actionLabel }}</span>
                                </span>
                            </span>
                        </li>
                    </ul>

                    <div class="tw-flex tw-justify-center tw-px-4 tw-py-10 sm:tw-px-6 tw-grow">
                        <div class="tw-grow tw-max-w-7xl">
                            <button type="button"
                                    class="tw-flex tw-items-center tw-mb-4 tw-text-xs tw-text-gray-500 hover:tw-text-gray-600 tw-gap-x-2">
                                <ArrowLeftLongIcon v-if="!rootSelected"
                                                   class="tw-w-3 tw-h-3 tw-shrink-0"
                                                   @click="store.selectParentPath()" />
                                <span v-for="rootLinePath in rootLine"
                                      :key="rootLinePath">
                                    / <span
                                          @click="selectPath(rootLinePath)">{{ getLabel(rootLinePath) }}</span>
                                </span>
                            </button>
                            <GenericItem :currentPath="selectedPath" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <ConfirmationDialog v-if="confirmationDialogOpen" />
</template>

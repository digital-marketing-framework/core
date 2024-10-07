import './assets/tailwind.css';
import 'tippy.js/dist/tippy.css'; // optional for styling
import './assets/custom.css';

import { linkEnvironments } from './utils/environmentLinker.js';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useDmfStore } from './stores/dmf.js';

// import { plugin as VueTippy } from 'vue-tippy';
import App from './App.vue';
import { clearCache } from '@/utils/processorCache';

function initApp(stage) {
  const app = createApp(App);

  app.use(createPinia());

  // app.use(VueTippy, {
  //   defaultProps: {
  //     placement: 'top',
  //     touch: false
  //   }
  // });

  app.mount(stage);

  return useDmfStore();
}

const init = async () => {

  linkEnvironments((environment) => {
    const store = initApp(environment.stage);
    store.$patch(environment);

    return {
      save: () => {
        store.save();
      },
      open: (data, inheritedData) => {
        store.$patch(environment);
        clearCache();
        store.receiveData({ data, inheritedData });
        store.open();
        store.triggerRerender();
      },
      close: () => {
        store.close();
      }
    };
  });
};

init();

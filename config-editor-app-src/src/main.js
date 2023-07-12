import './assets/tailwind.css';
import 'tippy.js/dist/tippy.css'; // optional for styling

import {
  EVENT_APP_OPEN,
  EVENT_APP_CLOSE,
  EVENT_APP_SAVE,
  linkEnvironment
} from './composables/environmentLinker.js';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useDmfStore } from './stores/dmf.js';

// import { plugin as VueTippy } from 'vue-tippy';
import App from './App.vue';

let store = null;

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
  const environment = await linkEnvironment();
  store = initApp(environment.stage);
  store.$patch(environment);

  document.addEventListener(EVENT_APP_SAVE, () => {
    store.save();
  });

  document.addEventListener(EVENT_APP_OPEN, (e) => {
    store.receiveData(e.detail);
    store.open();
  });

  document.addEventListener(EVENT_APP_CLOSE, () => {
    store.close();
  });
};

init();

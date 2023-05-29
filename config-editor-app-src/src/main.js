import './assets/tailwind.css';
import 'tippy.js/dist/tippy.css'; // optional for styling

import { linkEnvironment } from './composables/environmentLinker.js';

import { createApp } from 'vue';

import { createPinia } from 'pinia';

// import { plugin as VueTippy } from 'vue-tippy';
import App from './App.vue';

let started = false;
document.addEventListener('dmf-configuration-editor-app-start', e => {

    // TODO in modal mode the app can be closed and opened again without a page reload
    //      need to take this into account
    if (started) {
        return;
    }
    started = true;

    // TODO for now I am using a global variable, because vue did not go well well with async data fetching
    //      some components did not update correctly
    //      need to fix the cause and get rid of this workaround
    window.DMF_CONFIG_EDITOR = {
      loaded: true
    };
    Object.keys(e.detail).forEach(key => {
      window.DMF_CONFIG_EDITOR[key] = e.detail[key];
    });

    const app = createApp(App);

    app.use(createPinia());

    // app.use(VueTippy, {
    //   defaultProps: {
    //     placement: 'top',
    //     touch: false
    //   }
    // });

    app.mount(e.detail.stage);
});
document.dispatchEvent(new Event('dmf-configuration-editor-app-request'));

/**
 * dummy init method to emulate an environment
 * use window.start() to start the app with dummy data
 *
 * TODO remove this function eventually. how else to start in dev mode? how to differentiate bewteen production and dev?
 */
function initDummy() {
  let dummyStarted = false;
  window.start = function() {
      dummyStarted = true;
      document.dispatchEvent(new CustomEvent('dmf-configuration-editor-app-start', { detail: {
          stage: document.getElementById('app'),
          data: {
              'valueMaps': {
                  'countryMap': {
                      'DE': 'Germany',
                      'FR': 'France',
                      'US': 'United States'
                  }
              },
              'distributor': {
                  'routes': [
                      {
                          'type': 'pardot',
                          'pass': '',
                          'config': {
                              'pardot': {
                                  'enabled': true,
                                  'url': 'https://foobar.com/xyz'
                              }
                          }
                      }
                  ]
              }
          },
          inheritedData: {
              'distributor': {
                  'routes': [
                      {
                          'type': 'pardot',
                          'pass': '',
                          'config': {
                              'pardot': {
                                  'enabled': true,
                              }
                          }
                      }
                  ]
              }
          },
          schemaDocument: {
              valueSets: {
                  'route/all': ['pardot', 'mail']
              },
              types: {},
              schema: {
                  type: 'CONTAINER',
                  values: [
                      {
                          key: 'valueMaps',
                          type: 'MAP',
                          keyTemplate: {
                              type: 'STRING',
                              default: 'mapName'
                          },
                          valueTemplate: {
                              type: 'MAP',
                              keyTemplate: {
                                  type: 'STRING',
                                  default: 'mapKey'
                              },
                              valueTemplate: {
                                  type: 'STRING'
                              }
                          }
                      },
                      {
                          key: 'distributor',
                          type: 'CONTAINER',
                          values: [
                              {
                                  key: 'routes',
                                  type: 'LIST',
                                  valueTemplate: {
                                      type: 'SWITCH',
                                      values: [
                                          {
                                              key: 'type',
                                              type: 'STRING',
                                              allowedValues: {
                                                  'sets': ['route/all']
                                              }
                                          },
                                          {
                                              key: 'pass',
                                              type: 'STRING'
                                          },
                                          {
                                              key: 'config',
                                              type: 'CONTAINER',
                                              values: [
                                                  {
                                                      key: 'pardot',
                                                      type: 'CONTAINER',
                                                      values: [
                                                          {
                                                              key: 'enabled',
                                                              type: 'BOOLEAN'
                                                          },
                                                          {
                                                              key: 'url',
                                                              type: 'STRING',
                                                          }
                                                      ]
                                                  },
                                                  {
                                                      key: 'mail',
                                                      type: 'CONTAINER',
                                                      values: [
                                                          {
                                                              key: 'enabled',
                                                              type: 'BOOLEAN'
                                                          },
                                                          {
                                                              key: 'subject',
                                                              type: 'STRING',
                                                          }
                                                      ]
                                                  }
                                              ]
                                          }
                                      ]
                                  }
                              }
                          ]
                      }
                  ]
              }
          },
          settings: {
              readonly: false,
              mode: 'embedded'
          },
          onSave: data => new Promise(resolve => {
              console.log('=> save', data);
              setTimeout(() => { resolve(); }, 100);
          }),
          onIncludeChange: data => new Promise(resolve => {
              console.log('=> includes', data);
              setTimeout(() => { resolve(JSON.parse(JSON.stringify(data))); }, 100);
          })
      }}));
  };
  document.addEventListener('dmf-configuration-editor-app-request', () => {
    if (dummyStarted) {
      window.start();
    }
  });
}
initDummy();

linkEnvironment();

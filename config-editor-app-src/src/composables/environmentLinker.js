export const EVENT_INIT = 'dmf-configuration-editor-init';
export const EVENT_APP_REQUEST = 'dmf-configuration-editor-app-request';
export const EVENT_APP_START = 'dmf-configuration-editor-app-start';

const RAW_LANGUAGE = 'YAML';

let appEvent = null;
document.addEventListener(EVENT_APP_REQUEST, () => {
  setTimeout(() => {
    if (appEvent !== null) {
      document.dispatchEvent(appEvent);
    }
  }, 0);
});

function view(stage, data, inheritedData, schemaDocument, settings, onSave, onIncludeChange) {
  appEvent = new CustomEvent(EVENT_APP_START, {
    detail: {
      stage: stage,
      data: data,
      inheritedData: inheritedData,
      schemaDocument: schemaDocument,
      settings: settings,
      onSave: onSave,
      onIncludeChange: onIncludeChange
    }
  });
  document.dispatchEvent(appEvent);

  // for testing purposes only, to be used in the browser console
  // update data object by manipulating window.dce.data
  // save data object to textarea by executing window.dce.save()
  // update document includes by executing window.dce.updateIncludes()
  console.log('Hello DCE');
  window.dce = {
    stage: stage,
    data: data,
    inheritedData: inheritedData,
    schemaDocument: schemaDocument,
    settings: settings,
    onSave: onSave,
    onIncludeChange: onIncludeChange,
    save: function() {
      this.onSave(this.data)
        .then(() => { console.log('saved'); });
    },
    updateIncludes: function() {
      this.onIncludeChange(this.data)
        .then(response => {
          this.data = response.data;
          this.inheritedData = response.inheritedData;
          console.log('includes updated');
        });
    }
  };
}

async function ajaxFetch(url, payload) {
  const options = {
    method: payload !== null ? 'POST' : 'GET',
    headers: {}
  };
  if (payload) {
    options.method = 'POST';
    options.headers['Content-Type'] = 'application/json';
    options.body = JSON.stringify(payload);
  } else {
    options.method = 'GET';
  }

  const response = await fetch(url, options);
  return response.json();
}

async function getData(textarea, settings) {
  const document = textarea.value;
  let result;
  if (document) {
    result = await ajaxFetch(settings.urls.merge, {'document': document});
  } else {
    result = await ajaxFetch(settings.urls.defaults);
  }
  return result;
}

async function getSchema(settings) {
  return await ajaxFetch(settings.urls.schema);
}

async function setData(textarea, settings, data) {
  const response = await ajaxFetch(settings.urls.split, data);
  textarea.value = response.document;
  textarea.dispatchEvent(new Event('paste'), { bubbles: true});
  textarea.dispatchEvent(new Event('change'), { bubbles: true});
  textarea.dispatchEvent(new Event('input'), { bubbles: true});
  if (typeof textarea.onchange === 'function') {
    textarea.onchange();
  }
}

function cloneData(data) {
  return JSON.parse(JSON.stringify(data));
}

function getDocumentForm(textarea) {
  return textarea.closest('form');
}

async function save(textarea, settings, data) {
  await setData(textarea, settings, data);
  if (settings.mode === 'embedded') {
    getDocumentForm(textarea)?.submit();
  }
}

async function updateIncludes(settings, referenceData, newData) {
  return await ajaxFetch(settings.urls.updateIncludes, {'referenceData': referenceData, 'newData': newData});
}

function start(textarea, stage, settings) {
  let data, inheritedData, schema;
  let referenceData;
  Promise.all([
    getSchema(settings).then(response => {
      schema = response;
    }),
    getData(textarea, settings).then(response => {
      data = response.configuration;
      inheritedData = response.inheritedConfiguration;
    })
  ]).then(() => {
    referenceData = cloneData(data);
    view(
      stage,
      data,
      inheritedData,
      schema,
      settings,
      async (newData) => {
        await save(textarea, settings, newData);
      },
      async (newData) => {
        const response = await updateIncludes(settings, referenceData, newData);
        data = response.configuration;
        inheritedData = response.inheritedConfiguration;
        referenceData = cloneData(data);
        return {data: data, inheritedData: inheritedData};
      }
    );
  });
}

function setupEmbedded(textarea, settings) {
  const stage = document.createElement('DIV');
  stage.classList.add('dmf-configuration-document-editor-stage')
  textarea.parentNode.insertBefore(stage, textarea.nextSibling);
  start(textarea, stage, settings);
}

function setupModal(textarea, settings) {
  const stage = document.createElement('DIV');
  stage.classList.add('dmf-configuration-document-editor-stage');
  document.body.appendChild(stage);

  const startButton = document.createElement('BUTTON');
  textarea.parentNode.insertBefore(startButton, textarea.nextSibling);
  startButton.innerHTML = 'configure';
  startButton.classList.add('btn', 'btn-default');

  startButton.addEventListener('click', () => {
    start(textarea, stage, settings);
  });
}

function getSettings(textarea) {
  const ucfirst = s => s.substring(0, 1).toUpperCase() + s.substring(1);
  const urlKeys = ['schema', 'defaults', 'merge', 'split', 'updateIncludes'];
  const settings = {
    'urls': {},
    'rawLanguage': RAW_LANGUAGE
  };
  settings['mode'] = textarea.dataset.mode;
  settings['readonly'] = textarea.dataset.readonly === 'true';
  urlKeys.forEach(key => {
    settings.urls[key] = textarea.dataset['url' + ucfirst(key)];
  });
  return settings;
}

function setup(textarea) {
  const settings = getSettings(textarea);
  switch (settings.mode) {
    case 'embedded':
      setupEmbedded(textarea, settings);
      break;
    case 'modal':
      setupModal(textarea, settings);
      break;
    default:
      console.error('unknown editor mode "' + settings.mode + '"');
  }
}

export const linkEnvironment = function() {
  let textarea = document.querySelector('textarea.dmf-configuration-document');
  if (textarea !== null && textarea.dataset.app === 'true') {
    setup(textarea);
  } else {
    document.addEventListener(EVENT_INIT, () => {
      textarea = document.querySelector('textarea.dmf-configuration-document');
      setup(textarea);
    });
  }
};

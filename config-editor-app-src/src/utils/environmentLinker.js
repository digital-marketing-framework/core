import { cloneValue } from '@/helpers/value';

export const EVENT_INIT = 'dmf-configuration-editor-init';

const RAW_LANGUAGE = 'YAML';

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

const getData = async (textarea, settings) => {
  const document = textarea.value;
  let result;
  if (document) {
    result = await ajaxFetch(settings.urls.merge, { document: document });
  } else {
    result = await ajaxFetch(settings.urls.defaults);
    result = {
      configuration: result,
      inheritedConfiguration: cloneValue(result)
    };
  }
  return result;
};

const getSchema = async (settings) => {
  return await ajaxFetch(settings.urls.schema);
};

const setData = async (textarea, settings, data) => {
  const response = await ajaxFetch(settings.urls.split, data);
  textarea.value = response.document;
  textarea.dispatchEvent(new Event('paste'), { bubbles: true });
  textarea.dispatchEvent(new Event('change'), { bubbles: true });
  textarea.dispatchEvent(new Event('input'), { bubbles: true });
  if (typeof textarea.onchange === 'function') {
    textarea.onchange();
  }
};

const getDocumentForm = (textarea) => {
  return textarea.closest('form');
};

const getStageContainer = () => {
  return document.getElementById('stage-container');
};

const save = async (textarea, settings, data) => {
  await setData(textarea, settings, data);
};

const updateIncludes = async (settings, referenceData, newData) => {
  const payload = { referenceData: referenceData, newData: newData };
  return await ajaxFetch(settings.urls.updateIncludes, payload);
};

const updateTextArea = (textarea, stage, settings, start) => {
  if (settings.mode === 'modal') {
    const startButton = document.createElement('BUTTON');
    textarea.parentNode.insertBefore(startButton, textarea.nextSibling);
    startButton.innerHTML = 'configure';
    startButton.type = 'button';
    startButton.classList.add('btn', 'btn-default');

    startButton.addEventListener('click', () => {
      start(textarea, stage, settings);
    });
  }
  textarea.style.display = 'none';
};

const createStage = (isFixed) => {
  const stage = document.createElement('DIV');
  stage.classList.add('dmf-configuration-document-editor-stage');
  if(isFixed) {
    stage.style.position = 'fixed';
    stage.style.backgroundColor = 'rgba(0,0,0,0.5)';
    stage.style.zIndex = '9990';
  } else {
    stage.style.position = 'absolute';
  }
  stage.style.inset = '0';
  stage.style.display = 'none';
  return stage;
};

const setupEmbedded = () => {
  const stage = createStage(false);
  const stageContainer = getStageContainer();
  stageContainer.appendChild(stage);
  return stage;
};

const setupFullscreen = () => {
  const stage = createStage(true);
  const stageContainer = getStageContainer();
  stageContainer.appendChild(stage);
  return stage;
};

const setupModal = () => {
  const stage = createStage(true);
  document.body.appendChild(stage);
  return stage;
};

const getSettings = (textarea) => {
  const ucfirst = (s) => s.substring(0, 1).toUpperCase() + s.substring(1);
  const urlKeys = ['schema', 'defaults', 'merge', 'split', 'updateIncludes'];
  const settings = {
    urls: {},
    rawLanguage: RAW_LANGUAGE
  };
  settings['mode'] = textarea.dataset.mode;
  settings['readonly'] = textarea.dataset.readonly === 'true';
  settings['globalDocument'] = textarea.dataset.globalDocument === 'true';
  settings['debug'] = textarea.dataset.debug === 'true';
  urlKeys.forEach((key) => {
    settings.urls[key] = textarea.dataset['url' + ucfirst(key)];
  });
  return settings;
};

const triggerFormSave = (textarea) => {
  getDocumentForm(textarea)?.submit();
};

const triggerFormDiscard = (textarea) => {
  getDocumentForm(textarea)?.querySelector('[data-role="close"]')?.click();
};

const initEnvironment = async (textarea, link) => {
  let settings, stage, schemaDocument;
  let data, inheritedData, referenceData;
  let app;

  const onSave = async (newData) => {
    await save(textarea, settings, newData);
    if (settings.mode === 'modal') {
      app.close();
    } else {
      triggerFormSave(textarea);
    }
  };

  const onIncludeChange = async (newData) => {
    const response = await updateIncludes(settings, referenceData, newData);
    data = response.configuration;
    inheritedData = response.inheritedConfiguration;
    referenceData = cloneValue(data);
    return { data: data, inheritedData: inheritedData };
  };

  const onClose = async () => {
    if (settings.mode === 'modal') {
      stage.style.display = 'none';
    } else {
      triggerFormDiscard(textarea);
    }
  };

  settings = getSettings(textarea);
  schemaDocument = await getSchema(settings);

  const start = async () => {
    const response = await getData(textarea, settings);
    referenceData = cloneValue(response.configuration);
    app.open(response.configuration, response.inheritedConfiguration);
    stage.style.display = 'block';
  };

  if (settings.mode === 'modal') {
    stage = setupModal();
    updateTextArea(textarea, stage, settings, start);
  } else if (settings.mode == 'fullscreen') {
    stage = setupFullscreen(settings, start);
    setTimeout(() => {
      start(textarea, stage, settings);
    }, 0);
  } else {
    stage = setupEmbedded(settings, start);
    setTimeout(() => {
      start(textarea, stage, settings);
    }, 0);
  }

  app = link({
    settings: settings,
    stage: stage,
    schemaDocument: schemaDocument,
    onSave: onSave,
    onIncludeChange: onIncludeChange,
    onClose: onClose
  });
};

export const linkEnvironments = (link) => {
  function updateTextAreas() {
    document.querySelectorAll('textarea.dmf-configuration-document').forEach((textarea) => {
      if (!textarea.dataset.init) {
        textarea.dataset.init = '1';
        initEnvironment(textarea, link);
      }
    });
  }
  document.addEventListener(EVENT_INIT, updateTextAreas);
  updateTextAreas();
};

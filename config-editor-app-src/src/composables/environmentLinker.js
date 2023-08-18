import { cloneValue } from './valueHelper';

export const EVENT_INIT = 'dmf-configuration-editor-init';
export const EVENT_APP_OPEN = 'dmf-configuration-editor-app-open';
export const EVENT_APP_CLOSE = 'dmf-configuration-editor-app-close';
export const EVENT_APP_SAVE = 'dmf-configuration-editor-app-save';

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
    startButton.classList.add('btn', 'btn-default');

    startButton.addEventListener('click', () => {
      start(textarea, stage, settings);
    });
  }
  textarea.style.display = 'none';
};

const setupEmbedded = (textarea) => {
  const stage = document.createElement('DIV');
  stage.classList.add('dmf-configuration-document-editor-stage');
  const form = getDocumentForm(textarea);
  form.parentNode.insertBefore(stage, form.nextSibling);
  return stage;
};

const setupModal = () => {
  const stage = document.createElement('DIV');
  stage.classList.add('dmf-configuration-document-editor-stage');
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
  urlKeys.forEach((key) => {
    settings.urls[key] = textarea.dataset['url' + ucfirst(key)];
  });
  return settings;
};

const getInitialTextArea = async () => {
  return new Promise((resolve) => {
    const textarea = document.querySelector('textarea.dmf-configuration-document');
    if (textarea !== null && textarea.dataset.app === 'true') {
      setTimeout(() => {
        resolve(textarea);
      }, 0);
    } else {
      document.addEventListener(EVENT_INIT, () => {
        const textarea = document.querySelector('textarea.dmf-configuration-document');
        resolve(textarea);
      });
    }
  });
};

const getStageSiblings = (stage) => {
  const siblings = [];
  const children = stage.parentNode.children;
  for (let i = 0; i < children.length; i++) {
    const child = children[i];
    if (child !== stage) {
      siblings.push({ element: child, display: child.style.display });
    }
  }
  return siblings;
};

const triggerFormSave = (textarea) => {
  getDocumentForm(textarea)?.submit();
};

const triggerFormDiscard = (textarea) => {
  getDocumentForm(textarea)?.querySelector('[data-role="close"]')?.click();
};

export const linkEnvironment = async () => {
  let textarea, settings, stage, schemaDocument;
  let data, inheritedData, referenceData;
  let elementsToHide = null;

  const hideElements = () => {
    elementsToHide.forEach((e) => {
      e.element.style.display = 'none';
    });
  };

  const showElements = () => {
    elementsToHide.forEach((e) => {
      e.element.style.display = e.display;
    });
  };

  const onSave = async (newData) => {
    await save(textarea, settings, newData);
    if (settings.mode === 'modal') {
      document.dispatchEvent(new Event(EVENT_APP_CLOSE));
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
      showElements();
    } else {
      triggerFormDiscard(textarea);
    }
  };

  textarea = await getInitialTextArea();
  settings = getSettings(textarea);
  schemaDocument = await getSchema(settings);

  const start = async () => {
    const response = await getData(textarea, settings);
    referenceData = cloneValue(response.configuration);
    const appEvent = new CustomEvent(EVENT_APP_OPEN, {
      detail: {
        data: response.configuration,
        inheritedData: response.inheritedConfiguration
      }
    });
    document.dispatchEvent(appEvent);
    if (settings.mode === 'modal') {
      elementsToHide = getStageSiblings(stage);
    } else {
      const form = getDocumentForm(textarea);
      elementsToHide = [{ element: form, display: form.style.display }];
    }
    hideElements();
  };

  if (settings.mode === 'modal') {
    stage = setupModal();
    updateTextArea(textarea, stage, settings, start);
  } else {
    stage = setupEmbedded(textarea, settings, start);
    setTimeout(() => {
      start(textarea, stage, settings);
    }, 0);
  }

  document.addEventListener(EVENT_INIT, () => {
    textarea = document.querySelector('textarea.dmf-configuration-document');
    updateTextArea(textarea, stage, settings, start);
  });

  return {
    settings: settings,
    stage: stage,
    schemaDocument: schemaDocument,
    onSave: onSave,
    onIncludeChange: onIncludeChange,
    onClose: onClose
  };
};

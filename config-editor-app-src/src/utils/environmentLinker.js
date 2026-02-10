import { cloneValue } from '@/helpers/value';

export const EVENT_INIT = 'dmf-configuration-editor-init';

const RAW_LANGUAGE = 'YAML';

const INCLUDE_KEY = 'global-configuration--configuration-document--default';

/**
 * Context type to document name prefix mapping.
 */
const CONTEXT_TYPE_PREFIXES = {
  'form': 'Form',
  'form-plugin': 'Form Plugin',
  'api': 'API Endpoint',
};

/**
 * Get the document name prefix for a context type.
 *
 * @param {string} contextType - The context type
 * @returns {string} The prefix to use
 */
const getContextTypePrefix = (contextType) => {
  return CONTEXT_TYPE_PREFIXES[contextType] || '';
};

/**
 * Build a full document name with prefix.
 *
 * @param {string} documentName - The base document name
 * @param {string} contextType - The context type
 * @returns {string} The prefixed document name
 */
const buildPrefixedDocumentName = (documentName, contextType) => {
  const prefix = getContextTypePrefix(contextType);
  if (!documentName) {
    return '';
  }
  return prefix ? `${prefix}: ${documentName}` : documentName;
};

/**
 * Build a minimal YAML document with metaData.
 * If defaultDocument is provided, includes the default configuration document include.
 * Otherwise, includes an empty includes section.
 *
 * @param {string} documentName - The name to use in metaData.name
 * @param {string} contextType - The context type for prefix
 * @param {string} defaultDocument - The default document identifier (path), optional
 * @param {object} schemaVersions - Schema versions to include in metaData.version, optional
 * @returns {string} YAML document string
 */
const buildMetaDataYaml = (documentName, contextType, defaultDocument, schemaVersions = {}) => {
  const name = buildPrefixedDocumentName(documentName, contextType);
  const lines = [
    'metaData:',
    `    name: '${name}'`,
    '    strictValidation: true',
  ];

  // Add version section if schema versions are provided
  if (Object.keys(schemaVersions).length > 0) {
    lines.push('    version:');
    Object.entries(schemaVersions).forEach(([key, version]) => {
      lines.push(`        ${key}: '${version}'`);
    });
  }

  if (defaultDocument) {
    lines.push(
      '    includes:',
      `        ${INCLUDE_KEY}:`,
      `            uuid: ${INCLUDE_KEY}`,
      '            weight: 10000',
      `            value: '${defaultDocument}'`
    );
  } else {
    lines.push('    includes: {}');
  }

  return lines.join('\n');
};

const CONFIGURATION_DOCUMENT_TYPE = 'configuration-document';

const triggerTextareaUpdate = (textarea) => {
  function trigger() {
    textarea.dispatchEvent(new Event('paste', { bubbles: true }));
    textarea.dispatchEvent(new Event('change', { bubbles: true }));
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    textarea.dispatchEvent(new Event('keyup', { bubbles: true }));
    if (typeof textarea.onchange === 'function') {
      textarea.onchange();
    }
  }
  trigger();
  if ('requestIdleCallback' in window) {
    requestIdleCallback(trigger, { timeout: 1000 });
  }
  requestAnimationFrame(() => {
    requestAnimationFrame(trigger);
  });
  setTimeout(trigger, 100);
};

const updateTextarea = (textarea, value) => {
  textarea.value = value;
  triggerTextareaUpdate(textarea);
};

/**
 * Parse the YAML document to find the metaData.name field.
 * Scans line by line, tracking the current top-level key.
 *
 * @param {HTMLTextAreaElement} textarea - The textarea element
 * @returns {object} Parsed structure:
 *   - empty: whether the textarea content is empty
 *   - lines: array of all lines (empty array if content is empty)
 *   - nameLineIndex: index of the name line (-1 if not found)
 *   - nameValue: the name value without quotes (empty string if empty/missing)
 *   - nameIndent: the indentation string used for the name line
 *   - hasMetaData: whether metaData section exists
 *   - metaDataLineIndex: index of the metaData: line (-1 if not found)
 */
const parseDocument = (textarea) => {
  const content = textarea.value.trim();
  const lines = content === '' ? [] : content.split('\n');
  let empty = true;
  let currentTopLevelKey = '';
  let nameLineIndex = -1;
  let nameValue = '';
  let nameIndent = '    ';
  let hasMetaData = false;
  let metaDataLineIndex = -1;

  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    empty = false;

    // Skip empty lines and comments
    if (!line.trim() || line.trim().startsWith('#')) {
      continue;
    }

    // Check for top-level key (no leading whitespace)
    const topLevelMatch = line.match(/^([a-zA-Z_][a-zA-Z0-9_]*):/);
    if (topLevelMatch) {
      currentTopLevelKey = topLevelMatch[1];
      if (currentTopLevelKey === 'metaData') {
        hasMetaData = true;
        metaDataLineIndex = i;
      }
      continue;
    }

    // Check for name field when inside metaData
    if (currentTopLevelKey === 'metaData') {
      const nameMatch = line.match(/^(\s+)name:\s*(.*)$/);
      if (nameMatch) {
        nameLineIndex = i;
        nameIndent = nameMatch[1];
        const rawValue = nameMatch[2].trim();

        // Extract value from quotes if present
        const quotedMatch = rawValue.match(/^(['"])(.*)(\1)$/);
        if (quotedMatch) {
          nameValue = quotedMatch[2];
        } else {
          nameValue = rawValue;
        }
        break;
      }
    }
  }

  return {
    empty,
    lines,
    nameLineIndex,
    nameValue,
    nameIndent,
    hasMetaData,
    metaDataLineIndex,
  };
};

/**
 * Rebuild content from parsed structure with an updated name line.
 *
 * @param {object} parsed - The parsed document structure
 * @param {string} newName - The new name value
 * @returns {string} The updated content
 */
const rebuildWithName = (parsed, newName) => {
  const { lines, nameLineIndex, nameIndent, hasMetaData, metaDataLineIndex } = parsed;
  const newLines = [...lines];

  if (nameLineIndex >= 0) {
    // Replace existing name line
    newLines[nameLineIndex] = `${nameIndent}name: '${newName}'`;
  } else if (hasMetaData && metaDataLineIndex >= 0) {
    // Insert name line after metaData:
    newLines.splice(metaDataLineIndex + 1, 0, `${nameIndent}name: '${newName}'`);
  }

  return newLines.join('\n');
};

/**
 * Check if the document has an empty or missing metaData.name field.
 *
 * @param {object} parsed - The parsed document structure
 * @returns {boolean} True if name is empty or missing under metaData
 */
const hasEmptyDocumentName = (parsed) => {
  return parsed.hasMetaData && parsed.nameValue === '';
};

/**
 * Update the document name from "Form: X" to "Form Plugin: X".
 * Only updates if the current name starts with "Form: ".
 *
 * @param {HTMLTextAreaElement} textarea - The textarea element
 * @param {object} parsed - The parsed document structure
 */
const updateFormPluginDocumentName = (textarea, parsed) => {
  // Only update if name exists and starts with "Form: "
  if (parsed.empty || parsed.nameLineIndex < 0 || !parsed.nameValue.startsWith('Form: ')) {
    return;
  }

  const baseName = parsed.nameValue.substring('Form: '.length);
  const newName = `Form Plugin: ${baseName}`;
  const newContent = rebuildWithName(parsed, newName);

  updateTextarea(textarea, newContent);
};

/**
 * Add or update the document name when it's empty.
 *
 * @param {HTMLTextAreaElement} textarea - The textarea element
 * @param {object} parsed - The parsed document structure
 * @param {string} documentName - The base document name
 * @param {string} contextType - The context type for prefix
 */
const updateEmptyDocumentName = (textarea, parsed, documentName, contextType) => {
  if (parsed.empty || !documentName) {
    return;
  }

  const prefixedName = buildPrefixedDocumentName(documentName, contextType);
  if (!prefixedName) {
    return;
  }

  // Only update if metaData exists and name is empty
  if (!hasEmptyDocumentName(parsed)) {
    return;
  }

  const newContent = rebuildWithName(parsed, prefixedName);
  updateTextarea(textarea, newContent);
};

/**
 * Initialize embedded document textarea metaData.
 *
 * For form and api contexts:
 *   - Empty document: inject metaData with name and includes (or empty includes if no default)
 *   - Non-empty with empty name: add just the name
 * For form-plugin context:
 *   - Update the document name from "Form: X" to "Form Plugin: X"
 *
 * @param {HTMLTextAreaElement} textarea - The textarea element
 * @param {object} settings - The settings extracted from data attributes
 */
const initializeDocumentMetaData = (textarea, settings) => {
  // Only process non-global configuration documents
  if (settings.globalDocument || settings.documentType !== CONFIGURATION_DOCUMENT_TYPE) {
    return;
  }

  const parsed = parseDocument(textarea);

  // Special handling for form-plugins: only update document name, don't inject includes
  if (settings.contextType === 'form-plugin') {
    updateFormPluginDocumentName(textarea, parsed);
    return;
  }

  // For form/api contexts
  if (parsed.empty) {
    // Empty document: inject metaData with name, version, and includes (or empty includes if no default)
    updateTextarea(
      textarea,
      buildMetaDataYaml(settings.documentName, settings.contextType, settings.defaultDocument, settings.schemaVersions)
    );
  } else if (hasEmptyDocumentName(parsed)) {
    // Non-empty document with empty name: add just the name
    updateEmptyDocumentName(textarea, parsed, settings.documentName, settings.contextType);
  }
};

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
  updateTextarea(textarea, response.document);
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
  settings['contextIdentifier'] = textarea.dataset.contextIdentifier || '';
  settings['documentType'] = textarea.dataset.documentType || '';
  settings['documentGroup'] = textarea.dataset.documentGroup || '';
  settings['uid'] = textarea.dataset.uid || '';
  settings['defaultDocument'] = textarea.dataset.defaultDocument || '';
  settings['documentName'] = textarea.dataset.documentName || '';
  settings['contextType'] = textarea.dataset.contextType || '';
  settings['schemaVersions'] = textarea.dataset.schemaVersions ? JSON.parse(textarea.dataset.schemaVersions) : {};
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

  // Initialize metaData for embedded documents
  initializeDocumentMetaData(textarea, settings);

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

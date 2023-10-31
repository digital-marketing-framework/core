import YAML from 'yaml';

import prism from 'prismjs';
import 'prismjs/components/prism-yaml';

/**
 * example detail object:
 * {
 *   language: 'JSON',
 *   resolve: RESOLVE_FUNCTION,
 *   data: RAW_DATA_AS_STRING,
 * }
 */
export const EVENT_RAW_DATA_PARSE = 'dmf-configuration-editor--raw-data-parse';

export const rawDataParse = (language, data) => {
  let result = null;
  const e = new CustomEvent(EVENT_RAW_DATA_PARSE, {
    detail: {
      language: language,
      data: data,
      resolve: (_result) => {
        result = _result;
      }
    }
  });
  document.dispatchEvent(e);
  if (result === null) {
    throw new Error('unknown raw code language: ' + language);
  }
  return result;
};

document.addEventListener(EVENT_RAW_DATA_PARSE, (e) => {
  const language = e.detail.language;
  const resolve = e.detail.resolve;
  const rawData = e.detail.data;
  if (language === 'JSON') {
    resolve(JSON.parse(rawData));
  }
});

document.addEventListener(EVENT_RAW_DATA_PARSE, (e) => {
  const language = e.detail.language;
  const resolve = e.detail.resolve;
  const rawData = e.detail.data;
  if (language === 'YAML') {
    resolve(YAML.parse(rawData, { logLevel: 'silent' }));
  }
});

/**
 * example detail object:
 * {
 *   language: 'JSON',
 *   resolve: RESOLVE_FUNCTION,
 *   data: DATA_AS_OBJECT,
 * }
 */
export const EVENT_RAW_DATA_DUMP = 'dmf-configuration-editor--raw-data-dump';

export const rawDataDump = (language, data) => {
  let result = null;
  const e = new CustomEvent(EVENT_RAW_DATA_DUMP, {
    detail: {
      language: language,
      data: data,
      resolve: (_result) => {
        result = _result;
      }
    }
  });
  document.dispatchEvent(e);
  if (result === null) {
    throw new Error('unknown raw code language: ' + language);
  }
  return result;
};

document.addEventListener(EVENT_RAW_DATA_DUMP, (e) => {
  const language = e.detail.language;
  const resolve = e.detail.resolve;
  const data = e.detail.data;
  if (language === 'JSON') {
    resolve(JSON.stringify(data, null, 2));
  }
});

document.addEventListener(EVENT_RAW_DATA_DUMP, (e) => {
  const language = e.detail.language;
  const resolve = e.detail.resolve;
  const data = e.detail.data;
  if (language === 'YAML') {
    resolve(YAML.stringify(data));
  }
});

/**
 * example detail object:
 * {
 *   language: 'JSON',
 *   resolve: RESOLVE_FUNCTION
 * }
 */
export const EVENT_RAW_DATA_PRISM_HIGHLIGHTER =
  'dmf-configuration-editor--raw-data-prism-highlighter';

export const getPrismHighlighter = (language) => {
  let result = null;
  const e = new CustomEvent(EVENT_RAW_DATA_PRISM_HIGHLIGHTER, {
    detail: {
      language: language,
      resolve: (_result) => {
        result = _result;
      }
    }
  });
  document.dispatchEvent(e);
  if (result === null) {
    throw new Error('unknown raw code language: ' + language);
  }
  return result;
};

document.addEventListener(EVENT_RAW_DATA_PRISM_HIGHLIGHTER, (e) => {
  const language = e.detail.language;
  const resolve = e.detail.resolve;
  if (language === 'JSON') {
    const highlighter = (code) => prism.highlight(code, prism.languages.js);
    resolve(highlighter);
  }
});

document.addEventListener(EVENT_RAW_DATA_PRISM_HIGHLIGHTER, (e) => {
  const language = e.detail.language;
  const resolve = e.detail.resolve;
  if (language === 'YAML') {
    const highlighter = (code) => prism.highlight(code, prism.languages.yaml);
    resolve(highlighter);
  }
});

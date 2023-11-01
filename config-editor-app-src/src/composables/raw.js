import { getAbsolutePath } from '../helpers/path';
import { rawDataDump, rawDataParse } from '../helpers/rawValue';
import { useDmfStore } from '../stores/dmf';
import { useValidation } from './validation';

// TODO should we move the functionality of helpers/rawValue into this file?

const isRawView = (store, path, currentPath) =>
  !!store.rawViewPaths[getAbsolutePath(path, currentPath)];

const getRawValue = (store, path, currentPath) => {
  const value = store.getValue(path, currentPath, true);
  const language = store.settings.rawLanguage;
  return rawDataDump(language, value);
};

const getRawIssue = (store, path, currentPath) =>
  store.rawIssues[getAbsolutePath(path, currentPath)] || '';

// actions

const setRawValue = (store, path, currentPath, value) => {
  const currentValue = store.getValue(path, currentPath);
  try {
    const language = store.settings.rawLanguage;
    const dataFromString = rawDataParse(language, value);
    store.setValue(path, currentPath, dataFromString);

    // TODO we should distinguish between diffrent types of errors and not all of them should abort this process
    //      - soft validation errors for parent documents > should be ignored here
    //      - strict validation errors for all documents > should be ignored here
    //      - structure violations > should be taken into account and abort the process
    const { validate, getIssue } = useValidation(store);
    validate(path, currentPath);
    const issue = getIssue(path, currentPath, true);
    if (issue !== '') {
      throw new Error(issue);
    }

    unsetRawIssue(store, path, currentPath);
  } catch (e) {
    store.setValue(path, currentPath, currentValue);
    setRawIssue(store, path, currentPath, e.message);
  }
};

const setRawIssue = (store, path, currentPath, issue) => {
  if (typeof issue === 'undefined') {
    delete store.rawIssues[getAbsolutePath(path, currentPath)];
  } else {
    store.rawIssues[getAbsolutePath(path, currentPath)] = issue;
  }
};

const unsetRawIssue = (store, path, currentPath) => {
  setRawIssue(store, path, currentPath, undefined);
};

const toggleRawView = (store, path, currentPath) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  unsetRawIssue(store, path, currentPath);
  delete store.rawValues[absolutePath];
  store.rawViewPaths[absolutePath] = !store.rawViewPaths[absolutePath];
};

export const useRawProcessor = (store) => {
  store = store || useDmfStore();
  return {
    isRawView: (path, currentPath) => isRawView(store, path, currentPath),
    getRawValue: (path, currentPath) => getRawValue(store, path, currentPath),
    getRawIssue: (path, currentPath) => getRawIssue(store, path, currentPath),

    toggleRawView: (path, currentPath) => toggleRawView(store, path, currentPath),
    setRawValue: (path, currentPath, value) => setRawValue(store, path, currentPath, value),
    setRawIssue: (path, currentPath, issue) => setRawIssue(store, path, currentPath, issue),
    unsetRawIssue: (path, currentPath) => unsetRawIssue(store, path, currentPath)
  };
};

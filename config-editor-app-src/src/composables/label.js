import { getAbsolutePath, getLeafKey } from '../helpers/path';
import { useDmfStore } from '../stores/dmf';

const processLabel = (store, label, path, currentPath) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  let anyVariableFound = false;
  let variableFound = true;
  while (variableFound) {
    variableFound = false;
    label = label.replace(/\{[^}]+\}/, (match) => {
      variableFound = true;
      anyVariableFound = true;
      const referencePath = match.substring(1, match.length - 1);
      return store.getValue(referencePath, absolutePath);
    });
  }
  return anyVariableFound ? prettifyLabel(label) : label;
};

const prettifyLabel = (label) => {
  const ucfirst = (s) => s.substring(0, 1).toUpperCase() + s.substring(1);
  label = label.replace(/[A-Z]+/g, (match) => ' ' + match);
  label = label.replace(
    /[^a-zA-Z0-9]+([a-zA-Z0-9]+)/g,
    (wholeMatch, match) => ' ' + ucfirst(match)
  );
  label = label.replace(/[^a-zA-Z0-9]+$/, '');
  return ucfirst(label);
};

const getLabel = (store, path, currentPath) => {
  const schema = store.getSchema(path, currentPath, true);

  let label;
  if (schema.hideLabel) {
    label = '';
  } else if (schema.label) {
    label = schema.label;
    if (!schema.keepOriginalLabel) {
      label = processLabel(store, label, path, currentPath);
    }
  } else {
    label = prettifyLabel(getLeafKey(path, currentPath));
  }

  return label;
};

export const useLabelProcessor = (store) => {
  store = store || useDmfStore();
  return {
    getLabel: (path, currentPath) => getLabel(store, path, currentPath),
    processLabel: (label, path, currentPath) => processLabel(store, label, path, currentPath)
  };
};

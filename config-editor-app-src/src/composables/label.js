import { getAbsolutePath, getLeafKey } from '@/helpers/path';
import { useDmfStore } from '@/stores/dmf';
import { useValueSets } from './valueSets';

const processLabel = (store, label, path, currentPath, doNotPrettify) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  let rawValueFound = false;
  let variableFound = true;
  while (variableFound) {
    variableFound = false;
    label = label.replace(/\{[^{}]+\}/, (match) => {
      variableFound = true;
      const referencePath = match.substring(1, match.length - 1);
      const value = store.getValue(referencePath, absolutePath);
      let valueLabel;
      if (typeof value === 'object') {
        valueLabel = getLeafKey(referencePath, absolutePath);
        rawValueFound = true;
      } else {
        valueLabel = getValueLabel(store, value, referencePath, absolutePath);
        if (value === valueLabel) {
          rawValueFound = true;
        }
      }
      return valueLabel;
    });
  }
  return rawValueFound && !doNotPrettify ? prettifyLabel(label) : label;
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

const getValueLabel = (store, value, path, currentPath) => {
  const { getAllowedValues, getSuggestedValues } = useValueSets(store);
  const allowedValues = getAllowedValues(path, currentPath);
  if (typeof allowedValues[value] !== 'undefined') {
    return allowedValues[value];
  }

  const suggestedValues = getSuggestedValues(path, currentPath);
  if (typeof suggestedValues[value] !== 'undefined') {
    return suggestedValues[value];
  }

  return value;
};

export const useLabelProcessor = (store) => {
  store = store || useDmfStore();
  return {
    getLabel: (path, currentPath) => getLabel(store, path, currentPath),
    processLabel: (label, path, currentPath, doNotPrettify) => processLabel(store, label, path, currentPath, doNotPrettify),
    prettifyLabel: (label) => prettifyLabel(label),
    getValueLabel: (value, path, currentPath) => getValueLabel(store, value, path, currentPath)
  };
};

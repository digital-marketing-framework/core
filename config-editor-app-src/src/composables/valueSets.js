import { EVENT_GET_VALUE_PROCESSORS, EVENT_SET_VALUE_PROCESSOR } from './valueSetProcessors/events';

import './valueSetProcessors/listProcessor';
import './valueSetProcessors/referencesProcessor';
import './valueSetProcessors/setsProcessor';

import { getAbsolutePath } from '../helpers/path';
import { isNativeType } from '../helpers/type';
import { useDmfStore } from '../stores/dmf';

const valueProcessors = {};
document.addEventListener(EVENT_SET_VALUE_PROCESSOR, (e) => {
  valueProcessors[e.detail.keyword] = e.detail.processor;
});
document.dispatchEvent(
  new CustomEvent(EVENT_GET_VALUE_PROCESSORS, {
    detail: {
      addProcessor: (keyword, processor) => {
        valueProcessors[keyword] = processor;
      }
    }
  })
);

const getPredefinedValues = (store, valueConfig, currentPath) => {
  const values = {};
  Object.keys(valueConfig).forEach((keyword) => {
    if (typeof valueProcessors[keyword] !== 'undefined') {
      valueProcessors[keyword](
        store,
        valueConfig[keyword],
        currentPath,
        (value, label) => {
          // add
          if (typeof values[value] === 'undefined') {
            if (typeof label === 'undefined') {
              label = value;
            }
            values[value] = label;
          }
        },
        (value) => {
          // remove
          if (typeof values[value] !== 'undefined') {
            delete values[value];
          }
        }
      );
    }
  });
  return values;
};

const _getValues = (store, schema, field, currentPath) => {
  if (schema[field]) {
    return getPredefinedValues(store, schema[field], currentPath);
  }
  if (!isNativeType(schema.type)) {
    const customSchema = store.getCustomSchema(schema.type);
    return _getValues(store, customSchema, field, currentPath);
  }
  return {};
};

const _getAllowedValues = (store, schema, currentPath) =>
  _getValues(store, schema, 'allowedValues', currentPath);

const getAllowedValues = (store, path, currentPath) =>
  _getAllowedValues(store, store.getSchema(path, currentPath), getAbsolutePath(path, currentPath));

const _getFirstValue = (store, schema, field, currentPath) => {
  const values = _getValues(store, schema, field, currentPath);
  const keys = Object.keys(values);
  if (keys.length > 0) {
    return keys[0];
  }
  return null;
};

const _getFirstValueLabel = (store, schema, field, currentPath) => {
  const values = this._getValues(store, schema, field, currentPath);
  const keys = Object.keys(values);
  if (keys.length > 0) {
    return values[keys[0]];
  }
  return null;
};

const _getFirstAllowedValue = (store, schema, currentPath) =>
  _getFirstValue(store, schema, 'allowedValues', currentPath);

const getFirstAllowedValue = (store, path, currentPath) =>
  _getFirstAllowedValue(
    store,
    store.getSchema(path, currentPath),
    getAbsolutePath(path, currentPath)
  );

const _getFirstAllowedValueLabel = (store, schema, currentPath) =>
  _getFirstValueLabel(store, schema, 'allowedValues', currentPath);

const getFirstAllowedValueLabel = (store, path, currentPath) =>
  _getFirstAllowedValueLabel(
    store,
    store.getSchema(path, currentPath),
    getAbsolutePath(path, currentPath)
  );

const _getSuggestedValues = (store, schema, currentPath) =>
  _getValues(store, schema, 'suggestedValues', currentPath);

const getSuggestedValues = (store, path, currentPath) =>
  _getSuggestedValues(
    store,
    store.getSchema(path, currentPath),
    getAbsolutePath(path, currentPath)
  );

const _getFirstSuggestedValue = (store, schema, currentPath) =>
  _getFirstValue(store, schema, 'suggestedValues', currentPath);

const getFirstSuggestedValue = (store, path, currentPath) =>
  _getFirstSuggestedValue(
    store,
    store.getSchema(path, currentPath),
    getAbsolutePath(path, currentPath)
  );

const _getFirstSuggestedValueLabel = (store, schema, currentPath) =>
  _getFirstValueLabel(store, schema, 'suggestedValues', currentPath);

const getFirstSuggestedValueLabel = (store, path, currentPath) =>
  _getFirstSuggestedValueLabel(
    store,
    store.getSchema(path, currentPath),
    getAbsolutePath(path, currentPath)
  );

export const useValueSets = (store) => {
  store = store || useDmfStore();
  return {
    getPredefinedValues: (valueConfig, currentPath) =>
      getPredefinedValues(store, valueConfig, currentPath),
    getAllowedValues: (path, currentPath) => getAllowedValues(store, path, currentPath),
    getFirstAllowedValue: (path, currentPath) => getFirstAllowedValue(store, path, currentPath),
    getFirstAllowedValueLabel: (path, currentPath) =>
      getFirstAllowedValueLabel(store, path, currentPath),
    getSuggestedValues: (path, currentPath) => getSuggestedValues(store, path, currentPath),
    getFirstSuggestedValue: (path, currentPath) => getFirstSuggestedValue(store, path, currentPath),
    getFirstSuggestedValueLabel: (path, currentPath) =>
      getFirstSuggestedValueLabel(store, path, currentPath)
  };
};

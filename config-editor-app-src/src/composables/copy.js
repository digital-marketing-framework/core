import { v4 as uuidv4 } from 'uuid';
import { ListUtility } from '../helpers/listValue';
import { MapUtility } from '../helpers/mapValue';
import { getAbsolutePath } from '../helpers/path';
import { isContainerType } from '../helpers/type';
import { cloneValue } from '../helpers/value';
import { useDmfStore } from '../stores/dmf';

// actions

const _copyContainer = (store, schema, path, value) => {
  schema.values.forEach((childSchema) => {
    const childPath = childSchema.key;
    if (typeof value[childPath] !== 'undefined') {
      value[childPath] = _copyValue(
        store,
        childSchema,
        getAbsolutePath(childPath, path),
        value[childPath]
      );
    }
  });
};

const _copySwitch = (store, schema, path, value) => {
  schema.values.forEach((childSchema) => {
    const childPath = childSchema.key;
    switch (childPath) {
      case 'type': {
        break;
      }
      case 'config': {
        if (typeof value.config[value.type] !== 'undefined') {
          childSchema.values.forEach((configChildSchema) => {
            if (configChildSchema.key === value.type) {
              value.config[value.type] = _copyValue(
                store,
                configChildSchema,
                getAbsolutePath('config/' + value.type, path),
                value.config[value.type]
              );
            }
          });
        }
        break;
      }
      default: {
        if (typeof value[childPath] !== 'undefined') {
          value[childPath] = _copyValue(
            store,
            childSchema,
            getAbsolutePath(childPath, path),
            value[childPath]
          );
        }
      }
    }
  });
};

const _copyMap = (store, schema, path, value) => {
  const valueSchema = schema.itemTemplate.values.find((s) => s.key === MapUtility.KEY_VALUE);
  for (let id in value) {
    const newId = uuidv4();
    value[newId] = {};
    value[newId][MapUtility.KEY_UID] = newId;
    value[newId][MapUtility.KEY_WEIGHT] = value[id][MapUtility.KEY_WEIGHT];
    value[newId][MapUtility.KEY_KEY] = value[id][MapUtility.KEY_KEY];
    value[newId][MapUtility.KEY_VALUE] = _copyValue(
      store,
      valueSchema,
      getAbsolutePath(newId + '/' + ListUtility.KEY_VALUE, path),
      value[id][MapUtility.KEY_VALUE]
    );
    delete value[id];
  }
};

const _copyList = (store, schema, path, value) => {
  const valueSchema = schema.itemTemplate.values.find((s) => s.key === ListUtility.KEY_VALUE);
  for (let id in value) {
    const newId = uuidv4();
    value[newId] = {};
    value[newId][ListUtility.KEY_UID] = newId;
    value[newId][ListUtility.KEY_WEIGHT] = value[id][ListUtility.KEY_WEIGHT];
    value[newId][ListUtility.KEY_VALUE] = _copyValue(
      store,
      valueSchema,
      getAbsolutePath(newId + '/' + ListUtility.KEY_VALUE, path),
      value[id][ListUtility.KEY_VALUE]
    );
    delete value[id];
  }
};

const _copyValue = (store, schema, path, value) => {
  schema = store.resolveSchema(schema);
  if (isContainerType(schema.type)) {
    switch (schema.type) {
      case 'CONTAINER': {
        _copyContainer(store, schema, path, value);
        break;
      }
      case 'SWITCH': {
        _copySwitch(store, schema, path, value);
        break;
      }
      case 'MAP': {
        _copyMap(store, schema, path, value);
        break;
      }
      case 'LIST': {
        _copyList(store, schema, path, value);
        break;
      }
      default: {
        throw new Error('unknown container type "' + schema.type + '"');
      }
    }
  }
  return value;
};

const copyValue = (store, path, currentPath, value) => {
  const schema = store.getSchema(path, currentPath, true);
  const copy = cloneValue(value);
  return _copyValue(store, schema, getAbsolutePath(path, currentPath), copy);
};

export const useCopyProcessor = (store) => {
  store = store || useDmfStore();
  return {
    copyValue: (path, currentPath, value) => copyValue(store, path, currentPath, value)
  };
};

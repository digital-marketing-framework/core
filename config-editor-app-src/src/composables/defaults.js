import { ListUtility } from '../helpers/listValue';
import { MapUtility } from '../helpers/mapValue';
import { getAbsolutePath } from '../helpers/path';
import { cloneValue } from '../helpers/value';
import { useDmfStore } from '../stores/dmf';
import { UUID_PLACEHOLDER } from './dynamicItem';
import { usePathProcessor } from './path';
import { useValueSets } from './valueSets';

const _getDefaultList = (store, staticDefault, schema, path) => {
  if (staticDefault === null) {
    return {};
  }
  let valueSchema = null;
  schema.itemTemplate.values.forEach((itemSubSchema) => {
    if (itemSubSchema.key === ListUtility.KEY_VALUE) {
      valueSchema = itemSubSchema;
    }
  });
  if (valueSchema === null) {
    throw new Error('no list item value schema found');
  }
  const list = {};
  for (let index in staticDefault) {
    ListUtility.append(
      list,
      _getDefaultValue(
        store,
        valueSchema,
        getAbsolutePath(UUID_PLACEHOLDER + '/' + ListUtility.KEY_VALUE, path),
        staticDefault[index]
      )
    );
  }
  return list;
};

const _getDefaultMap = (store, staticDefault, schema, path) => {
  if (staticDefault === null) {
    return {};
  }
  let valueSchema = null;
  schema.itemTemplate.values.forEach((itemSubSchema) => {
    if (itemSubSchema.key === MapUtility.KEY_VALUE) {
      valueSchema = itemSubSchema;
    }
  });
  if (valueSchema === null) {
    throw new Error('no map item value schema found');
  }
  const map = {};
  for (let key in staticDefault) {
    MapUtility.append(
      map,
      key,
      _getDefaultValue(
        store,
        valueSchema,
        getAbsolutePath(UUID_PLACEHOLDER + '/' + MapUtility.KEY_VALUE, path),
        staticDefault[key]
      )
    );
  }
  return map;
};

const _getDefaultContainer = (store, staticDefault, schema, path) => {
  let defaultValue = {};
  if (staticDefault !== null) {
    defaultValue = staticDefault;
  }
  schema.values.forEach((subSchema) => {
    defaultValue[subSchema.key] = _getDefaultValue(
      store,
      subSchema,
      getAbsolutePath(subSchema.key, path),
      defaultValue[subSchema.key]
    );
  });
  return defaultValue;
};

const _getDefaultSwitch = (store, staticDefault, schema, path) => {
  let defaultValue = {};
  if (staticDefault !== null) {
    defaultValue = staticDefault;
  }
  let defaultType = null;
  let configSchema = null;
  schema.values.forEach((subSchema) => {
    switch (subSchema.key) {
      case 'config': {
        configSchema = subSchema;
        break;
      }
      case 'type': {
        defaultType = _getDefaultValue(
          store,
          subSchema,
          getAbsolutePath('type', path),
          defaultValue.type
        );
        defaultValue.type = defaultType;
        break;
      }
      default: {
        defaultValue[subSchema.key] = _getDefaultValue(
          store,
          subSchema,
          getAbsolutePath(subSchema.key, path),
          defaultValue[subSchema.key]
        );
        break;
      }
    }
  });
  if (defaultType === null) {
    throw new Error('no "type" property found in switch schema');
  }
  if (configSchema === null) {
    throw new Error('no "config" property found in switch schema');
  }
  defaultValue.config = defaultValue.config || {};
  configSchema.values.forEach((subSchema) => {
    if (subSchema.key === defaultType) {
      defaultValue.config[defaultType] = _getDefaultValue(
        store,
        subSchema,
        getAbsolutePath('config/' + defaultType, path),
        defaultValue.config[defaultType]
      );
    }
  });
  return defaultValue;
};

const _getDefaultValue = (store, schema, path, staticDefault) => {
  schema = store.resolveSchema(schema);

  if (typeof staticDefault === 'undefined') {
    staticDefault = null;
  }
  if (staticDefault === null) {
    if (typeof schema.default !== 'undefined') {
      staticDefault = cloneValue(schema.default);
    }
  }
  const { getFirstAllowedValue, getFirstSuggestedValue } = useValueSets(store);
  if (staticDefault === null) {
    // TODO we already have the schema, can we pass it instead of the path?
    const firstAllowedValue = getFirstAllowedValue(path);
    if (firstAllowedValue !== null) {
      staticDefault = firstAllowedValue;
    }
  }
  if (staticDefault === null) {
    // TODO we already have the schema, can we pass it instead of the path?
    const firstSuggestedValue = getFirstSuggestedValue(path);
    if (firstSuggestedValue !== null) {
      staticDefault = firstSuggestedValue;
    }
  }

  switch (schema.type) {
    case 'LIST': {
      return _getDefaultList(store, staticDefault, schema, path);
    }
    case 'MAP': {
      return _getDefaultMap(store, staticDefault, schema, path);
    }
    case 'CONTAINER': {
      return _getDefaultContainer(store, staticDefault, schema, path);
    }
    case 'SWITCH': {
      return _getDefaultSwitch(store, staticDefault, schema, path);
    }
    case 'STRING': {
      if (staticDefault !== null) {
        return staticDefault;
      }
      return '';
    }
    case 'INTEGER': {
      if (staticDefault !== null) {
        return staticDefault;
      }
      return 0;
    }
    case 'BOOLEAN': {
      if (staticDefault !== null) {
        return staticDefault;
      }
      return false;
    }
    default: {
      throw new Error('unknown schema type ' + schema.type);
    }
  }
};

const getDefaultValue = (store, path, currentPath) => {
  const schema = store.getSchema(path, currentPath, true);
  return _getDefaultValue(store, schema, getAbsolutePath(path, currentPath));
};

// actions

const updateValue = (store, path, currentPath) => {
  const value = store.getValue(path, currentPath);
  if (typeof value === 'undefined') {
    store.setValue(path, currentPath, getDefaultValue(store, path, currentPath));
  } else {
    const { getChildPaths } = usePathProcessor(store);
    const absolutePath = getAbsolutePath(path, currentPath);
    getChildPaths(path, currentPath).forEach((childPath) => {
      updateValue(store, childPath, absolutePath);
    });
  }
};

export const useDefaults = (store) => {
  store = store || useDmfStore();
  return {
    getDefaultValue: (path, currentPath) => getDefaultValue(store, path, currentPath),
    updateValue: (path, currentPath) => updateValue(store, path, currentPath)
  };
};

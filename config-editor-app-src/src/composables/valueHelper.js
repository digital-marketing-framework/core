export const EVENT_GET_VALUES = 'dmf-configuration-editor--get-values';

export const cloneValue = (value) => {
  // console.log('cloneValue', value);
  if (typeof value === 'undefined') {
    return undefined;
  }
  return JSON.parse(JSON.stringify(value));
};

export const mergeValue = (source, target, excludeKeys) => {
  excludeKeys = excludeKeys || [];
  Object.keys(source).forEach((key) => {
    if (excludeKeys.indexOf(key) === -1) {
      const targetType = typeof target[key];
      if (targetType === 'undefined') {
        target[key] = source[key];
      } else {
        const sourceType = typeof source[key];
        if (
          targetType !== sourceType ||
          Array.isArray(source[key]) !== Array.isArray(target[key])
        ) {
          throw new Error("values don't seem to have the same type");
        }
        if (sourceType === 'object') {
          mergeValue(source[key], target[key]);
        } else {
          target[key] = source[key];
        }
      }
    }
  });
};

export const flip = (map) => {
  return Object.entries(map).reduce((obj, [key, value]) => ({ ...obj, [value]: key }), {});
};

export const valuesEqual = (a, b) => {
  if (typeof a === 'object' && typeof b === 'object') {
    for (let key in a) {
      if (typeof b[key] === 'undefined') {
        return false;
      }
      if (!valuesEqual(a[key], b[key])) {
        return false;
      }
    }
    for (let key in b) {
      if (typeof a[key] === 'undefined') {
        return false;
      }
    }
    return true;
  } else {
    return a === b;
  }
};

/**
 * example detail object:
 * {
 *   store: STORE_OBJECT,
 *   add: ADD_VALUE_FUNCTION,
 *   type: 'list',
 *   config: {
 *     'value1': 'label1',
 *     'value2': 'label2'
 *   }
 * }
 */
document.addEventListener(EVENT_GET_VALUES, (e) => {
  if (e.detail.type !== 'list') {
    return;
  }
  const config = e.detail.config;
  const add = e.detail.add;
  Object.keys(config).forEach((value) => {
    add(value, config[value]);
  });
});

/**
 * example detail object:
 * {
 *   store: STORE_OBJECT,
 *   add: ADD_VALUE_FUNCTION,
 *   type: 'sets',
 *   config: [
 *     'setName',
 *     'setName2'
 *   ]
 * }
 */
document.addEventListener(EVENT_GET_VALUES, (e) => {
  if (e.detail.type !== 'sets') {
    return;
  }
  const config = e.detail.config;
  const store = e.detail.store;
  const add = e.detail.add;
  const schemaDocument = store.schemaDocument;
  config.forEach((setName) => {
    const set = schemaDocument.valueSets[setName] || {};
    Object.keys(set).forEach((value) => {
      add(value, set[value]);
    });
  });
});

/**
 * example detail object (space between * and / is necessary to not close the block comment and should be ignored):
 * {
 *   store: STORE_OBJECT,
 *   add: ADD_VALUE_FUNCTION,
 *   type: 'references',
 *   currentPath: '/some/path',
 *   config: [
 *     {path: '/foo/bar', label:'foo-bar'},
 *     {'/foo/* /bar'},
 *     {'../foo/bar/*', label:'{baz}'}
 *   ]
 * }
 */
document.addEventListener(EVENT_GET_VALUES, (e) => {
  if (e.detail.type !== 'references') {
    return;
  }
  const config = e.detail.config;
  const store = e.detail.store;
  const add = e.detail.add;
  const currentPath = e.detail.path;
  config.forEach((reference) => {
    const paths = store.getAllPaths(reference.path, currentPath);
    paths.forEach((path) => {
      switch (reference.type) {
        case 'key': {
          const value = store.getLeafKey(path);
          const label = reference.label ? store._processLabel(reference.label, path) : value;
          add(value, label);
          break;
        }
        case 'value': {
          add(store.getValue(path));
          break;
        }
        default: {
          throw new Error('unknown reference type "' + reference.type + '"');
        }
      }
    });
  });
});

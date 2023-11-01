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

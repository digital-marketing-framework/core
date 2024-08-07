import { cloneValue } from '../helpers/value';

let cache = {};

export const cacheManager = {
  key: function (path, currentPath) {
    return (currentPath || '/') + '::' + path;
  },
  get: function (domain, key, keepReference) {
    cache[domain] = cache[domain] || {};
    const result = cache[domain][key];
    return typeof result === 'object' && !keepReference ? cloneValue(result) : result;
  },
  set: function (domain, key, data, keepReference) {
    cache[domain] = cache[domain] || {};
    cache[domain][key] = typeof data === 'object' && !keepReference ? cloneValue(data) : data;
  },
  flush: function (domain) {
    if (typeof domain === 'undefined') {
      cache = {};
    } else {
      cache[domain] = {};
    }
  }
};

export const cached = (domain, keys, uncached, keepReference) => {
  const key = keys.join('::');
  let result = cacheManager.get(domain, key, keepReference);
  if (typeof result !== 'undefined') {
    return result;
  }
  result = uncached();
  if (typeof result !== 'undefined') {
    cacheManager.set(domain, key, result, keepReference);
  }
  return result;
};

export const clearCache = () => {
  cache = {};
};

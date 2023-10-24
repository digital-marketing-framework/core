import { cloneValue } from './valueHelper';

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

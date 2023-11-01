import { EVENT_GET_CONDITION_PROCESSORS, EVENT_SET_CONDITION_PROCESSOR } from './events';

const keyword = 'equals';

/**
 * config: {
 *   'path': '/foo/bar',
 *   'value': 'someValue'
 * }
 */
const processor = (store, config, currentPath) => {
  const value = store.getValue(config.path, currentPath);
  // we use non-strict comparison to not have to deal with type conversion
  return config.value == value;
};

document.addEventListener(EVENT_GET_CONDITION_PROCESSORS, (e) => {
  e.detail.addProcessor(keyword, processor);
});

document.dispatchEvent(
  new CustomEvent(EVENT_SET_CONDITION_PROCESSOR, {
    detail: {
      keyword: keyword,
      processor: processor
    }
  })
);

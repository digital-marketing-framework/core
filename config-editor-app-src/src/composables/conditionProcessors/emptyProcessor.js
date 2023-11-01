import { EVENT_GET_CONDITION_PROCESSORS, EVENT_SET_CONDITION_PROCESSOR } from './events';

const keyword = 'empty';

/**
 * config: {
 *   'path': '/foo/bar'
 * }
 */
const processor = (store, config, currentPath) => {
  const value = store.getValue(config.path, currentPath);
  return value === '';
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

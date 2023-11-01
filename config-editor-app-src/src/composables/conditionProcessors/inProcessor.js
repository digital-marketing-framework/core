import { useValueSets } from '../valueSets';
import { EVENT_GET_CONDITION_PROCESSORS, EVENT_SET_CONDITION_PROCESSOR } from './events';

const keyword = 'in';

/**
 * config: {
 *   'path': '/foo/bar',
 *   'list': { VALUE_CONFIG }
 * }
 */
const processor = (store, config, currentPath) => {
  const value = store.getValue(config.path, currentPath);
  const { getPredefinedValues } = useValueSets(store);
  const list = getPredefinedValues(config.list, currentPath);
  for (let key in list) {
    // we use non-strict comparison to not have to deal with type conversion
    if (key == value) {
      return true;
    }
  }
  return false;
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

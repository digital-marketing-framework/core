import { EVENT_GET_VALUE_PROCESSORS, EVENT_SET_VALUE_PROCESSOR } from './events';

const keyword = 'list';

/**
 * config: {
 *   'value1': 'label1',
 *   'value2': 'label2'
 * }
 */
const processor = (store, config, currentPath, add) => {
  Object.keys(config).forEach((value) => {
    add(value, config[value]);
  });
};

document.addEventListener(EVENT_GET_VALUE_PROCESSORS, (e) => {
  e.detail.addProcessor(keyword, processor);
});

document.dispatchEvent(
  new CustomEvent(EVENT_SET_VALUE_PROCESSOR, {
    detail: {
      keyword: keyword,
      processor: processor
    }
  })
);

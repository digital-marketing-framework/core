import { EVENT_GET_CONDITION_PROCESSORS, EVENT_SET_CONDITION_PROCESSOR } from './events';

const keyword = 'not';

/**
 * config: { SUB_CONDITION_CONFIG }
 */
const processor = (store, config, currentPath, evaluate) => {
  return !evaluate(config, currentPath);
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

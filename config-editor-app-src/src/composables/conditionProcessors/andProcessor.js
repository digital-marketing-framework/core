import { EVENT_GET_CONDITION_PROCESSORS, EVENT_SET_CONDITION_PROCESSOR } from './events';

const keyword = 'and';

/**
 * config: [
 *   SUB_CONDITION_CONFIG_1,
 *   SUB_CONDITION_CONFIG_2,
 *   SUB_CONDITION_CONFIG_3
 * ]
 */
const processor = (store, config, currentPath, evaluate) => {
  for (let index = 0; index < config.length; index++) {
    if (!evaluate(config[index], currentPath)) {
      return false;
    }
  }
  return true;
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

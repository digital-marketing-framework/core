import { EVENT_GET_VALUE_PROCESSORS, EVENT_SET_VALUE_PROCESSOR } from './events';

const keyword = 'sets';

/**
 * config: [
 *   'setName',
 *   'setName2'
 * ]
 */
const processor = (store, config, currentPath, add) => {
  const schemaDocument = store.schemaDocument;
  config.forEach((setName) => {
    const set = schemaDocument.valueSets[setName] || {};
    Object.keys(set).forEach((value) => {
      add(value, set[value]);
    });
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

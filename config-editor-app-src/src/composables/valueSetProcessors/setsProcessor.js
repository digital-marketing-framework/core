import { EVENT_GET_VALUE_PROCESSORS, EVENT_SET_VALUE_PROCESSOR } from './events';
import { useLabelProcessor } from '@/composables/label';

const keyword = 'sets';

/**
 * config: [
 *   'setName',
 *   'setName2'
 * ]
 */
const processor = (store, config, currentPath, add) => {
  const { processLabel } = useLabelProcessor(store);
  const valueSets = store.schemaDocument.valueSets;
  config.forEach((setName) => {
    const processedSetName = processLabel(setName, currentPath, null, true);
    const set = valueSets[processedSetName] || {};
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

import { useFieldContextRepository } from '../fieldContext/fieldContextRepository';
import { EVENT_GET_VALUE_PROCESSORS, EVENT_SET_VALUE_PROCESSOR } from './events';

const inputKeyword = 'inputFieldContextSelection';
const outputKeyword = 'outputFieldContextSelection';

/**
 * config: []
 */
const inputProcessor = (store, config, currentPath, add) => {
  const { getInputContextNames } = useFieldContextRepository(store);
  const names = getInputContextNames(currentPath);
  for (let key in names) {
    add(key, names[key]);
  }
};

/**
 * config: []
 */
const outputProcessor = (store, config, currentPath, add) => {
  const { getOutputContextNames } = useFieldContextRepository(store);
  const names = getOutputContextNames();
  for (let key in names) {
    add(key, names[key]);
  }
};

document.addEventListener(EVENT_GET_VALUE_PROCESSORS, (e) => {
  e.detail.addProcessor(inputKeyword, inputProcessor);
});

document.dispatchEvent(
  new CustomEvent(EVENT_SET_VALUE_PROCESSOR, {
    detail: {
      keyword: inputKeyword,
      processor: inputProcessor
    }
  })
);

document.addEventListener(EVENT_GET_VALUE_PROCESSORS, (e) => {
  e.detail.addProcessor(outputKeyword, outputProcessor);
});

document.dispatchEvent(
  new CustomEvent(EVENT_SET_VALUE_PROCESSOR, {
    detail: {
      keyword: outputKeyword,
      processor: outputProcessor
    }
  })
);

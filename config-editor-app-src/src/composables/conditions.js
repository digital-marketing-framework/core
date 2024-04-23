import { useDmfStore } from '../stores/dmf';
import {
  EVENT_GET_CONDITION_PROCESSORS,
  EVENT_SET_CONDITION_PROCESSOR
} from './conditionProcessors/events';

import './conditionProcessors/andProcessor';
import './conditionProcessors/emptyProcessor';
import './conditionProcessors/equalsProcessor';
import './conditionProcessors/inProcessor';
import './conditionProcessors/notEmptyProcessor';
import './conditionProcessors/notProcessor';
import './conditionProcessors/orProcessor';
import './conditionProcessors/uniqueProcessor';

const conditionProcessors = {};
document.addEventListener(EVENT_SET_CONDITION_PROCESSOR, (e) => {
  conditionProcessors[e.detail.keyword] = e.detail.processor;
});
document.dispatchEvent(
  new CustomEvent(EVENT_GET_CONDITION_PROCESSORS, {
    detail: {
      addProcessor: (keyword, processor) => {
        conditionProcessors[keyword] = processor;
      }
    }
  })
);

const evaluate = (store, condition, currentPath) => {
  if (typeof conditionProcessors[condition.type] === 'undefined') {
    throw new Error('unknown condition type "' + condition.type + '"');
  }
  return conditionProcessors[condition.type](
    store,
    condition.config,
    currentPath,
    (subCondition, subCurrentPath) => evaluate(store, subCondition, subCurrentPath)
  );
};

export const useConditions = (store) => {
  store = store || useDmfStore();
  return {
    evaluate: (config, currentPath) => evaluate(store, config, currentPath)
  };
};

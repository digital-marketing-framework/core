import { getAbsolutePath } from '../../helpers/path';
import { usePathProcessor } from '../path';
import { EVENT_GET_CONDITION_PROCESSORS, EVENT_SET_CONDITION_PROCESSOR } from './events';

const keyword = 'unique';

/**
 * config: {
 *   valuePath: 'foo',
 *   pathPattern: '../* /foo',
 * }
 */
const processor = (store, config, currentPath) => {
  const valuePath = getAbsolutePath(config.valuePath, currentPath);
  const value = store.getValue(valuePath);
  const pathPattern = config.pathPattern;
  const { getAllPaths } = usePathProcessor(store);
  let paths = getAllPaths(pathPattern, currentPath);
  for (let index = 0; index < paths.length; index++) {
    const path = paths[index];
    if (path !== valuePath && store.getValue(path) === value) {
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

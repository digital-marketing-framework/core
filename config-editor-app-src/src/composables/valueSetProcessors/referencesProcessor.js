import { getLeafKey } from '../../helpers/path';
import { useLabelProcessor } from '../label';
import { usePathProcessor } from '../path';
import { EVENT_GET_VALUE_PROCESSORS, EVENT_SET_VALUE_PROCESSOR } from './events';

const keyword = 'references';
/**
 * config: [
 *   {type: 'value', path: '/foo/bar', label:'foo-bar'},
 *   {type: 'value', path: '/foo/* /bar'},
 *   {type: 'key', path: '../foo/bar/*', label:'{baz}'}
 * ]
 */
const processor = (store, config, currentPath, add) => {
  config.forEach((reference) => {
    const { getAllPaths } = usePathProcessor(store);
    const { processLabel, getLabel, getValueLabel, prettifyLabel } = useLabelProcessor(store);
    const paths = getAllPaths(reference.path, currentPath);
    paths.forEach((path) => {
      switch (reference.type) {
        case 'key': {
          const value = getLeafKey(path);
          const label = reference.label ? processLabel(reference.label, path) : getLabel(path);
          add(value, label);
          break;
        }
        case 'value': {
          const value = store.getValue(path);
          let label = value;
          if (reference.label) {
            label = processLabel(reference.label, path);
          } else {
            label = getValueLabel(value, path);
            if (value === label) {
              label = prettifyLabel(label);
            }
          }
          add(value, label);
          break;
        }
        default: {
          throw new Error('unknown reference type "' + reference.type + '"');
        }
      }
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

import { useFieldRoles } from '../fieldContext/fieldRoles';
import { useFieldContextRepository } from '../fieldContext/fieldContextRepository';
import { useFieldContextProcessor } from '../fieldContext/fieldContextProcessor';
import { EVENT_GET_VALUE_PROCESSORS, EVENT_SET_VALUE_PROCESSOR } from './events';

const keyword = 'contextual';

/**
 * config: []
 */
const processor = (store, config, currentPath, add) => {
  const { getRoles } = useFieldRoles(store);
  const roles = getRoles(currentPath);
  roles.forEach((role) => {
    switch (role) {
      case 'inputField': {
        const { getActiveInputContext } = useFieldContextRepository(store);
        const context = getActiveInputContext(currentPath);
        const { getLabel } = useFieldContextProcessor(store);
        for (let name in context) {
          add(name, getLabel(context, name));
        }
        break;
      }
      case 'outputField': {
        const { getActiveOutputContext } = useFieldContextRepository(store);
        const context = getActiveOutputContext(currentPath);
        const { getLabel } = useFieldContextProcessor(store);
        for (let name in context) {
          add(name, getLabel(context, name));
        }
        break;
      }
    }
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

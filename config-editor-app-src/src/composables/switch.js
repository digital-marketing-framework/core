import { getAbsolutePath, getLeafKey } from '../helpers/path';
import { useDmfStore } from '../stores/dmf';
import { useDefaults } from './defaults';

const processSwitchChange = (store, path, currentPath, newValue) => {
  const lastPathPart = getLeafKey(path, currentPath);
  if (lastPathPart !== 'type') {
    return;
  }
  const parentSchema = store.getParentSchema(path, currentPath, true);
  if (parentSchema.type !== 'SWITCH') {
    return;
  }
  const type = typeof newValue === 'undefined' ? store.getValue(path, currentPath, true) : newValue;
  const { updateValue } = useDefaults(store);
  updateValue(path + '/../config/' + type, currentPath);
};

const cleanupSwitchConfig = (store, path, currentPath) => {
  const schema = store.getSchema(path, currentPath, true);
  const absolutePath = getAbsolutePath(path, currentPath);
  if (schema.type === 'SWITCH') {
    const switchObject = store.getValue(path, currentPath);
    const selectedType = switchObject.type;
    Object.keys(switchObject.config).forEach((type) => {
      if (selectedType !== type && !store.isInherited('config/' + type, absolutePath)) {
        delete switchObject.config[type];
      }
    });
  }
};

export const useSwitch = (store) => {
  store = store || useDmfStore();
  return {
    processSwitchChange: (path, currentPath, newValue) =>
      processSwitchChange(store, path, currentPath, newValue),
    cleanupSwitchConfig: (path, currentPath) => cleanupSwitchConfig(store, path, currentPath)
  };
};

import { ListUtility } from '../helpers/listValue';
import { getAbsolutePath } from '../helpers/path';
import { useDmfStore } from '../stores/dmf';
import { WARNING_INCLUDES_CHANGED, useNotifications } from './notifications';

const getDocumentName = (store) => {
  return store.data.metaData.name;
};

const includesChanged = (store) => {
  const changed = !ListUtility.equals(
    store.data.metaData.includes || {},
    store.referenceIncludes || {}
  );
  const { setWarning, unsetWarning } = useNotifications(store);
  if (changed) {
    setWarning(
      WARNING_INCLUDES_CHANGED,
      () => {
        updateIncludes(store);
      },
      'Apply changes'
    );
  } else {
    unsetWarning(WARNING_INCLUDES_CHANGED);
  }
  return changed;
};

const isIncludeList = (path, currentPath) =>
  getAbsolutePath(path, currentPath) === '/metaData/includes';

// actions

const updateIncludes = async (store) => {
  const response = await store.onIncludeChange(store.data);
  store.data = response.data;
  store.inheritedData = response.inheritedData;
  store.initData();
  const { unsetWarning, writeMessage } = useNotifications(store);
  unsetWarning(WARNING_INCLUDES_CHANGED);
  writeMessage('Includes updated successfully!');
};

export const useDocument = (store) => {
  store = store || useDmfStore();
  return {
    getDocumentName: () => getDocumentName(store),
    includesChanged: () => includesChanged(store),
    isIncludeList: (path, currentPath) => isIncludeList(path, currentPath),

    updateIncludes: async () => await updateIncludes(store)
  };
};

import { ListUtility } from '../helpers/listValue';
import { MapUtility } from '../helpers/mapValue';
import { getAbsolutePath, getParentPath, isRoot } from '../helpers/path';
import { isDynamicContainerType } from '../helpers/type';
import { useDmfStore } from '../stores/dmf';
import { useCopyProcessor } from './copy';
import { useDefaults } from './defaults';
import { useNavigation } from './navigation';

export const UUID_PLACEHOLDER = 'NEW';

const isDynamicContainer = (store, path, currentPath) =>
  isDynamicContainerType(store.getSchema(path, currentPath, true).type);

const isDynamicChild = (store, path, currentPath) =>
  !isRoot(path, currentPath) && isDynamicContainer(store, getParentPath(path, currentPath));

const isFirstChild = (store, path, currentPath) => {
  if (!isDynamicChild(store, path, currentPath)) {
    throw new Error('first item check not possible on non-dynamic items');
  }
  const item = store.getValue(path, currentPath);
  const list = store.getParentValue(path, currentPath);
  return ListUtility.isFirst(list, item[ListUtility.KEY_UID]);
};

const isLastChild = (store, path, currentPath) => {
  if (!isDynamicChild(store, path, currentPath)) {
    throw new Error('last item check not possible on non-dynamic items');
  }
  const item = store.getValue(path, currentPath);
  const list = store.getParentValue(path, currentPath);
  return ListUtility.isLast(list, item[ListUtility.KEY_UID]);
};

// actions

const getContainerUtility = (store, path, currentPath) => {
  const schema = store.getSchema(path, currentPath, true);
  switch (schema.type) {
    case 'LIST':
      return ListUtility;
    case 'MAP':
      return MapUtility;
  }
  throw new Error('schema ' + schema.type + ' is not a dynamic container');
};

const moveValueUp = (store, path, currentPath) => {
  if (!isDynamicChild(store, path, currentPath)) {
    throw new Error('cannot move non-dynamic items');
  }
  const containerUtility = getContainerUtility(store, getParentPath(path, currentPath));
  const list = store.getParentValue(path, currentPath);
  const item = store.getValue(path, currentPath);
  const previousItem = containerUtility.findPredecessor(list, containerUtility.getItemId(item));
  if (previousItem !== null) {
    containerUtility.moveBefore(
      list,
      containerUtility.getItemId(item),
      containerUtility.getItemId(previousItem)
    );
  }
};

const moveValueDown = (store, path, currentPath) => {
  if (!isDynamicChild(store, path, currentPath)) {
    throw new Error('cannot move non-dynamic items');
  }
  const containerUtility = getContainerUtility(store, getParentPath(path, currentPath));
  const list = store.getParentValue(path, currentPath);
  const item = store.getValue(path, currentPath);
  const nextItem = containerUtility.findSuccessor(list, containerUtility.getItemId(item));
  if (nextItem !== null) {
    containerUtility.moveAfter(
      list,
      containerUtility.getItemId(item),
      containerUtility.getItemId(nextItem)
    );
  }
};

const addValue = (store, path, currentPath, value) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  if (!isDynamicContainer(store, path, currentPath)) {
    throw new Error(absolutePath + ': cannot add items to a non-dynamic container');
  }
  const schema = store.getSchema(path, currentPath, true);
  const container = store.getValue(path, currentPath, true);
  const { getDefaultValue } = useDefaults(store);
  if (schema.type === 'LIST') {
    if (typeof value === 'undefined') {
      value = getDefaultValue(UUID_PLACEHOLDER + '/' + ListUtility.KEY_VALUE, absolutePath, true);
    }
    ListUtility.append(container, value);
  } else if (schema.type === 'MAP') {
    if (typeof value === 'undefined') {
      value = getDefaultValue(UUID_PLACEHOLDER + '/' + MapUtility.KEY_VALUE, absolutePath, true);
    }
    const defaultKey = getDefaultValue(UUID_PLACEHOLDER + '/' + MapUtility.KEY_KEY, absolutePath);
    MapUtility.append(container, defaultKey, value);
  }
  const { expandContainer } = useNavigation(store);
  expandContainer(path, currentPath);
};

const copyValue = (store, path, currentPath) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  if (!isDynamicChild(store, absolutePath)) {
    throw new Error('non-dynamic items cannot be copied');
  }
  const { copyValue } = useCopyProcessor(store);
  const item = store.getValue(absolutePath);
  const value = item[ListUtility.KEY_VALUE];
  const copy = copyValue(path + '/' + ListUtility.KEY_VALUE, currentPath, value);
  addValue(store, '..', absolutePath, copy);
};

const removeValue = (store, path, currentPath) => {
  if (!isDynamicChild(store, path, currentPath)) {
    throw new Error('non-dynamic items cannot be removed');
  }
  const container = store.getParentValue(path, currentPath);
  const item = store.getValue(path, currentPath);
  const containerUtility = getContainerUtility(store, getParentPath(path, currentPath));
  containerUtility.remove(container, containerUtility.getItemId(item));
  if (getAbsolutePath(path, currentPath) === store.selectedPath) {
    store.selectParentPath();
  }
};

export const useDynamicProcessor = (store) => {
  store = store || useDmfStore();
  return {
    isDynamicContainer: (path, currentPath) => isDynamicContainer(store, path, currentPath),
    isDynamicChild: (path, currentPath) => isDynamicChild(store, path, currentPath),
    isFirstChild: (path, currentPath) => isFirstChild(store, path, currentPath),
    isLastChild: (path, currentPath) => isLastChild(store, path, currentPath),

    moveValueUp: (path, currentPath) => moveValueUp(store, path, currentPath),
    moveValueDown: (path, currentPath) => moveValueDown(store, path, currentPath),
    addValue: (path, currentPath) => addValue(store, path, currentPath),
    removeValue: (path, currentPath) => removeValue(store, path, currentPath),
    copyValue: (path, currentPath) => copyValue(store, path, currentPath)
  };
};

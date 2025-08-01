import { getAbsolutePath, isRoot } from '../helpers/path';
import { isContainerType } from '../helpers/type';
import { useDmfStore } from '../stores/dmf';
import { usePathProcessor } from './path';

const NAVIGATION_STORAGE_KEY_PREFIX = 'anyrel-config-editor-navigation-';
const navigationStorage = window.sessionStorage;

// TODO this method is probably not useful because the different components
//      have the schema anyway and then they jsut need to read one variable
//      instead of calling this method, which would fetch the schema again
const skipHeader = (store, path, currentPath) => {
  const schema = store.getSchema(path, currentPath, true);
  if (schema.skipHeader) {
    return true;
  }
  return false;
};

const skipInNavigation = (store, path, currentPath) => {
  const schema = store.getSchema(path, currentPath, true);
  if (typeof schema.skipInNavigation !== 'undefined') {
    return schema.skipInNavigation;
  }
  return false;
};

const isNavigationItem = (store, path, currentPath) => {
  const schema = store.getSchema(path, currentPath, true);
  if (typeof schema.navigationItem !== 'undefined') {
    return !!schema.navigationItem;
  }
  return isContainerType(schema.type);
};

const getNavigationChildPaths = (store, path, currentPath, absolute) => {
  const { getChildPaths } = usePathProcessor(store);
  const absolutePath = getAbsolutePath(path, currentPath);
  const childPaths = getChildPaths(path, currentPath, absolute);
  const navigationChildPaths = [];
  childPaths.forEach((childPath) => {
    if (isNavigationItem(store, childPath, absolutePath)) {
      if (skipInNavigation(store, childPath, absolutePath)) {
        let childChildPaths = getNavigationChildPaths(store, childPath, absolutePath, absolute);
        if (!absolute) {
          childChildPaths = childChildPaths.map(
            (childChildPath) => childPath + '/' + childChildPath
          );
        }
        navigationChildPaths.push(...childChildPaths);
      } else {
        navigationChildPaths.push(childPath);
      }
    }
  });
  return navigationChildPaths;
};

const getContainerState = (store, path, currentPath) => {
  const { isSelected } = usePathProcessor(store);
  if (isSelected(path, currentPath)) {
    return true;
  }
  const absolutePath = getAbsolutePath(path, currentPath);
  if (typeof store.collapsedContainerPaths[absolutePath] === 'undefined') {
    const schema = store.getSchema(path, currentPath, true);
    // TODO setup schema rendering property "closedInitially", also, should it be closed initially or open initially?
    const { getSelectedPath } = usePathProcessor(store);
    store.collapsedContainerPaths[absolutePath] =
      getSelectedPath() === absolutePath || (schema.openInitially ? true : false);
  }
  return store.collapsedContainerPaths[absolutePath];
};

const getContainerNavigationState = (store, path, currentPath) => {
  if (isRoot(path, currentPath)) {
    return true;
  }
  const absolutePath = getAbsolutePath(path, currentPath);
  if (typeof store.collapsedMenuPaths[absolutePath] === 'undefined') {
    // TODO should the default nav state be open or closed or configurable in schema?
    store.collapsedMenuPaths[absolutePath] = false;
  }
  return store.collapsedMenuPaths[absolutePath];
};

// actions

const expandContainer = (store, path, currentPath) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  const discloseBtn = document.querySelector('[data-current-path="' + absolutePath + '"]');
  if(discloseBtn.dataset.headlessuiState !== "open") discloseBtn?.click();
};

const setContainerState = (store, path, currentPath, open) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  store.collapsedContainerPaths[absolutePath] = open;
};

const toggleContainerState = (store, path, currentPath) => {
  const open = getContainerState(store, path, currentPath);
  setContainerState(store, path, currentPath, !open);
};

const toggleContainerNavigationState = (store, path, currentPath) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  store.collapsedMenuPaths[absolutePath] = !store.collapsedMenuPaths[absolutePath];
};

const saveNavigationState = (store) => {
  if (!store.settings.uid) {
    return;
  }

  const key = NAVIGATION_STORAGE_KEY_PREFIX + store.settings.uid;
  const value = {
    selectedPath: store.selectedPath,
    collapsedMenuPaths: store.collapsedMenuPaths,
    collapsedContainerPaths: store.collapsedContainerPaths,
  };
  navigationStorage.setItem(key, JSON.stringify(value));
};

const loadNavigationState = (store) => {
  const { selectPath } = usePathProcessor(store);
  let navigation = {};

  if (store.settings.uid) {
    const key = NAVIGATION_STORAGE_KEY_PREFIX + store.settings.uid;
    let savedNavigation = navigationStorage.getItem(key);
    if (savedNavigation) {
      savedNavigation = JSON.parse(savedNavigation);
      if (typeof savedNavigation === 'object') {
        navigation = savedNavigation;
      }
    }
  }

  store.collapsedMenuPaths = navigation.collapsedMenuPaths || {};
  store.collapsedContainerPaths = navigation.collapsedContainerPaths || {};
  selectPath(navigation.selectedPath || '/');
};

export const useNavigation = (store) => {
  store = store || useDmfStore();
  return {
    skipHeader: (path, currentPath) => skipHeader(store, path, currentPath),
    skipInNavigation: (path, currentPath) => skipInNavigation(store, path, currentPath),
    isNavigationItem: (path, currentPath) => isNavigationItem(store, path, currentPath),
    getNavigationChildPaths: (path, currentPath, absolute) =>
      getNavigationChildPaths(store, path, currentPath, absolute),
    getContainerState: (path, currentPath) => getContainerState(store, path, currentPath),
    getContainerNavigationState: (path, currentPath) =>
      getContainerNavigationState(store, path, currentPath),
    expandContainer: (path, currentPath) => expandContainer(store, path, currentPath),
    setContainerState: (path, currentPath, open) =>
      setContainerState(store, path, currentPath, open),
    toggleContainerState: (path, currentPath) => toggleContainerState(store, path, currentPath),
    toggleContainerNavigationState: (path, currentPath) =>
      toggleContainerNavigationState(store, path, currentPath),
    loadNavigationState: () => loadNavigationState(store),
    saveNavigationState: () => saveNavigationState(store)
  };
};

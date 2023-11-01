import { getAbsolutePath, isRoot } from '../helpers/path';
import { isContainerType } from '../helpers/type';
import { useDmfStore } from '../stores/dmf';
import { usePathProcessor } from './path';

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
  // TODO a changed container state is not taken into account immediately
  //      it is only read when the component is re-rendered
  //      that is also why the opening animation is missing
  //      how to open the Disclosure thingy properly?
  setContainerState(store, path, currentPath, true);
  store.triggerRerender();
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
      toggleContainerNavigationState(store, path, currentPath)
  };
};

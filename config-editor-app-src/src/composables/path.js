import { useDmfStore } from '../stores/dmf';
import { ListUtility } from '../helpers/listValue';
import { MapUtility } from '../helpers/mapValue';
import { getAbsolutePath, isRoot } from '../helpers/path';
import { useNavigation } from './navigation';

const getChildPaths = (store, path, currentPath, absolute) => {
  const schema = store.getSchema(path, currentPath, true);
  const absolutePath = getAbsolutePath(path, currentPath);
  switch (schema.type) {
    case 'SWITCH': {
      const paths = [];
      for (let index in schema.values) {
        const childSchema = schema.values[index];
        if (childSchema.key === 'config') {
          const type = store.getValue(path + '/type', currentPath, true);
          paths.push(absolute ? absolutePath + '/config/' + type : 'config/' + type);
        } else {
          paths.push(absolute ? absolutePath + '/' + childSchema.key : childSchema.key);
        }
      }
      return paths;
    }
    case 'CONTAINER': {
      const paths = [];
      const values = schema.values.sort(
        (childSchema1, childSchema2) => childSchema1.weight - childSchema2.weight
      );
      values.forEach((childSchema) => {
        paths.push(absolute ? absolutePath + '/' + childSchema.key : childSchema.key);
      });
      return paths;
    }
    case 'MAP': {
      const map = store.getValue(path, currentPath, true);
      const pathPrefix = absolute ? absolutePath + '/' : '';
      const paths = [];
      Object.keys(MapUtility.sort(map)).forEach((id) => {
        paths.push(pathPrefix + id);
      });
      return paths;
    }
    case 'LIST': {
      const list = store.getValue(path, currentPath, true);
      const pathPrefix = absolute ? absolutePath + '/' : '';
      const paths = [];
      Object.keys(ListUtility.sort(list)).forEach((id) => {
        paths.push(pathPrefix + id);
      });
      return paths;
    }
    case 'STRING':
    case 'INTEGER':
    case 'BOOLEAN': {
      return [];
    }
    default: {
      throw new Error('unknown schema type "' + schema.type + '"');
    }
  }
};

const getChildPathsGrouped = (store, path, currentPath, absolute) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  const childPaths = getChildPaths(store, path, currentPath, absolute);
  const result = {};
  childPaths.forEach((childPath) => {
    const schema = store.getSchema(childPath, absolutePath, true);
    const group = typeof schema.group !== 'undefined' ? schema.group : 'global';
    if (typeof result[group] === 'undefined') {
      result[group] = [];
    }
    result[group].push(childPath);
  });
  return result;
};

const getAllPaths = (store, pathPattern, currentPath) => {
  pathPattern = getAbsolutePath(pathPattern, currentPath);
  if (pathPattern === '/') {
    return [pathPattern];
  }
  let paths = [''];
  pathPattern
    .substring(1)
    .split('/')
    .forEach((pathPart) => {
      if (pathPart === '*') {
        const newPaths = [];
        paths.forEach((path) => {
          getChildPaths(store, path).forEach((childPath) => {
            newPaths.push(path + '/' + childPath);
          });
        });
        paths = newPaths;
      } else {
        for (let index = 0; index < paths.length; index++) {
          paths[index] = paths[index] + '/' + pathPart;
        }
      }
    });
  return paths;
};

const isPathSelectable = (store, path, currentPath) => {
  // TODO cachable?
  const { isNavigationItem, skipInNavigation } = useNavigation(store);
  return isNavigationItem(path, currentPath) && !skipInNavigation(path, currentPath);
};

const getClosestSelectablePath = (store, path, currentPath) => {
  // TODO cachable?
  const { skipInNavigation, isNavigationItem } = useNavigation(store);
  const absolutePath = getAbsolutePath(path, currentPath);
  const pathParts = absolutePath === '/' ? [] : absolutePath.split('/');
  let resultPath = '/';
  let nextPath = '/';
  while (pathParts.length > 0) {
    nextPath = getAbsolutePath(pathParts.shift(), nextPath);
    if (!isNavigationItem(nextPath)) {
      break;
    }
    if (!skipInNavigation(nextPath)) {
      resultPath = nextPath;
    }
  }
  return resultPath;
};

const isSelected = (store, path, currentPath) =>
  store.selectedPath === getAbsolutePath(path, currentPath);

const getSelectedPath = (store) => store.selectedPath;

// actions

const selectPath = (store, path, currentPath) => {
  store.selectedPath = getClosestSelectablePath(store, path, currentPath);
  // TODO if requested path is not the selected path, scroll to the corresponding field. how?
  // store.triggerRerender();
};

const selectParentPath = (store) => {
  if (!isRoot(store.selectedPath)) {
    selectPath(store, '..', store.selectedPath);
  }
};

export const usePathProcessor = (store) => {
  store = store || useDmfStore();
  return {
    getChildPaths: (path, currentPath, absolute) =>
      getChildPaths(store, path, currentPath, absolute),
    getChildPathsGrouped: (path, currentPath, absolute) =>
      getChildPathsGrouped(store, path, currentPath, absolute),
    getAllPaths: (pathPattern, currentPath) => getAllPaths(store, pathPattern, currentPath),
    isPathSelectable: (path, currentPath) => isPathSelectable(store, path, currentPath),
    getClosestSelectablePath: (path, currentPath) =>
      getClosestSelectablePath(store, path, currentPath),
    isSelected: (path, currentPath) => isSelected(store, path, currentPath),
    getSelectedPath: () => getSelectedPath(store),

    selectPath: (path, currentPath) => selectPath(store, path, currentPath),
    selectParentPath: () => selectParentPath(store)
  };
};

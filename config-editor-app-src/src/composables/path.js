import { useDmfStore } from '../stores/dmf';
import { ListUtility } from '../helpers/listValue';
import { MapUtility } from '../helpers/mapValue';
import { getAbsolutePath, getLeafKey, isRoot } from '../helpers/path'
import { useNavigation } from './navigation';

const getChildPaths = (store, path, currentPath, absolute) => {
  const schema = store.getSchema(path, currentPath, true);
  const absolutePath = getAbsolutePath(path, currentPath);
  switch (schema.type) {
    case 'SWITCH': {
      const paths = [];
      const values = schema.values.sort(
        (childSchema1, childSchema2) => childSchema1.weight - childSchema2.weight
      );
      for (let index in values) {
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

const pathMatchesSinglePattern = (path, pathPattern, excludeSubPaths) => {
  if (!pathPattern) {
    return false;
  }
  const pathParts = path.split('/');
  const pathPatternParts = pathPattern.split('/');
  while (pathPatternParts.length > 0) {
    if (pathParts.length === 0) {
      return false;
    }

    const pathPart = pathParts.shift();
    const pathPatternPart = pathPatternParts.shift();
    if (pathPatternPart !== '*' && pathPart !== pathPatternPart) {
      return false;
    }
  }
  if (pathParts.length === 0 && pathPatternParts.length === 0) {
    return true;
  }
  return !excludeSubPaths;
};

const pathMatchesPattern = (path, pathPattern, excludeSubPaths) => {
  if (!pathPattern) {
    return false;
  }
  if (typeof pathPattern === 'string') {
    pathPattern = [pathPattern];
  }
  for (let i = 0; i < pathPattern.length; i++) {
    if (pathMatchesSinglePattern(path, pathPattern[i], excludeSubPaths)) {
      return true;
    }
  }
  return false;
};

const processPathPattern = (store, pathPattern, currentPath) => {
  return pathPattern.replace(/\{[^}]+\}/, (match) => {
    const referencePath = match.substring(1, match.length - 1);
    let value = store.getValue(referencePath, currentPath);
    if (typeof value === 'object') {
      value = getLeafKey(referencePath, currentPath);
    }
    return typeof value === 'undefined' || value === '' ? '*' : value;
  });
};

const getAllPaths = (store, pathPattern, currentPath, ignorePathPattern) => {
  pathPattern = getAbsolutePath(pathPattern, currentPath);
  if (pathPattern === '/') {
    return [pathPattern];
  }
  pathPattern = processPathPattern(store, pathPattern, currentPath);
  if (typeof ignorePathPattern === 'string') {
    ignorePathPattern = [ignorePathPattern];
  }
  if (typeof ignorePathPattern === 'object') {
    for (let i = 0; i < ignorePathPattern.length; i++) {
      ignorePathPattern[i] = processPathPattern(store, ignorePathPattern[i], currentPath);
    }
  }
  let paths = [''];
  pathPattern
    .substring(1)
    .split('/')
    .forEach((pathPart) => {
      if (pathPart === '*') {
        const newPaths = [];
        paths.forEach((path) => {
          if (!pathMatchesPattern(path, ignorePathPattern)) {
            getChildPaths(store, path).forEach((childPath) => {
              const newPath = path + '/' + childPath;
              if (!pathMatchesPattern(newPath, ignorePathPattern)) {
                newPaths.push(newPath);
              }
            });
          }
        });
        paths = newPaths;
      } else {
        for (let index = 0; index < paths.length; index++) {
          const newPath = paths[index] + '/' + pathPart;
          if (!pathMatchesPattern(newPath, ignorePathPattern)) {
            paths[index] = paths[index] + '/' + pathPart;
          }
        }
      }
    });
  return paths;
};

const isPathSelectable = (store, path, currentPath) => {
  // TODO cacheable?
  const { isNavigationItem, skipInNavigation } = useNavigation(store);
  return isNavigationItem(path, currentPath) && !skipInNavigation(path, currentPath);
};

const getClosestSelectablePath = (store, path, currentPath) => {
  // TODO cacheable?
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

const isOutboundRoutePath = (store, path, currentPath) => {
  // /integrations/NAME/outboundRoutes/ID/value/type
  const matches = getAbsolutePath(path, currentPath).match(
    /^\/integrations\/([^/]+)\/outboundRoutes\/([^/]+)\//
  );
  if (matches === null) {
    return false;
  }
  const integration = matches[1];
  const id = matches[2];
  return {
    integration: integration,
    keyword: store.data.integrations[integration].outboundRoutes[id].value.type
  };
};

const isInboundRoutePath = (store, path, currentPath) => {
  // /integrations/NAME/inboundRoutes/TYPE
  const matches = getAbsolutePath(path, currentPath).match(
    /^\/integrations\/([^/]+)\/inboundRoutes\/([^/]+)\//
  );
  if (matches === null) {
    return false;
  }
  return {
    integration: matches[1],
    keyword: matches[2]
  };
};

const isDataMapperGroupPath = (store, path, currentPath) => {
  // /dataProcessing/dataMapperGroup/ID
  const matches = getAbsolutePath(path, currentPath).match(/^\/dataProcessing\/dataMapperGroups\/([^/]+)/);
  if (matches === null) {
    return false;
  }
  return matches[1];
};

const isConditionPath = (store, path, currentPath) => {
  // /dataProcessing/conditions/ID
  const matches = getAbsolutePath(path, currentPath).match(
    /^\/dataProcessing\/conditions\/([^/]+)/
  );
  if (matches === null) {
    return false;
  }
  return matches[1];
};

const isPersonalizationPath = (store, path, currentPath) => {
  return getAbsolutePath(path, currentPath).startsWith('/personalization/');
};

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
    getAllPaths: (pathPattern, currentPath, ignorePathPattern) =>
      getAllPaths(store, pathPattern, currentPath, ignorePathPattern),
    isPathSelectable: (path, currentPath) => isPathSelectable(store, path, currentPath),
    getClosestSelectablePath: (path, currentPath) =>
      getClosestSelectablePath(store, path, currentPath),
    isSelected: (path, currentPath) => isSelected(store, path, currentPath),
    getSelectedPath: () => getSelectedPath(store),

    isOutboundRoutePath: (path, currentPath) => isOutboundRoutePath(store, path, currentPath),
    isInboundRoutePath: (path, currentPath) => isInboundRoutePath(store, path, currentPath),
    isDataMapperGroupPath: (path, currentPath) => isDataMapperGroupPath(store, path, currentPath),
    isConditionPath: (path, currentPath) => isConditionPath(store, path, currentPath),
    isPersonalizationPath: (path, currentPath) => isPersonalizationPath(store, path, currentPath),

    selectPath: (path, currentPath) => selectPath(store, path, currentPath),
    selectParentPath: () => selectParentPath(store)
  };
};

import { cached } from '../utils/processorCache';

const pathIsAbsolute = (path) => path.toString().startsWith('/');

const sanitizePath = (path) => {
  path = path.toString();
  if (path !== '/' && path.toString().endsWith('/')) {
    path = path.slice(0, -1);
  }
  path = path.replace('\\/\\/+', '\\/');
  return path;
};

const simplifyPath = (absolutePath) => {
  absolutePath = sanitizePath(absolutePath);
  if (!pathIsAbsolute(absolutePath)) {
    throw new Error('path needs to be absolute');
  }
  const resultPathParts = [];
  const pathParts = absolutePath.substring(1).split('/');
  pathParts.forEach((pathPart) => {
    switch (pathPart) {
      case '.': {
        // do nothing
        break;
      }
      case '..': {
        // one level up
        const previousPathPart = resultPathParts.pop();
        if (typeof previousPathPart === 'undefined') {
          throw new Error('path seems to go beyond absolute path bounds');
        }
        break;
      }
      default: {
        resultPathParts.push(pathPart);
        break;
      }
    }
  });
  return '/' + resultPathParts.join('/');
};

export const getAbsolutePath = (path, currentPath) => {
  return cached('getAbsolutePath', [path, currentPath], () => {
    path = sanitizePath(path);
    currentPath = sanitizePath(currentPath || '/');
    if (!path.startsWith('/')) {
      if (!pathIsAbsolute(currentPath)) {
        throw new Error('current path needs to be absolute');
      }
      path = currentPath === '/' ? '/' + path : currentPath + '/' + path;
    }
    return simplifyPath(path);
  });
};

export const getLeafKey = (path, currentPath) =>
  getAbsolutePath(path, currentPath).split('/').pop();

export const isRoot = (path, currentPath) => getAbsolutePath(path, currentPath) === '/';

export const getParentPath = (path, currentPath) => getAbsolutePath(path + '/..', currentPath);

export const getPathParts = (path, currentPath) => {
  return cached('getPathParts', [path, currentPath], () => {
    const pathPartsString = getAbsolutePath(path, currentPath).substring(1);
    if (pathPartsString === '') {
      return [];
    }
    return pathPartsString.split('/');
  });
};

export const getRootLine = (path, currentPath) => {
  return cached('getRootLine', [path, currentPath], () => {
    const pathParts = getPathParts(path, currentPath);
    let currentRootLinePath = '/';
    const rootLine = ['/'];
    pathParts.forEach((pathPart) => {
      currentRootLinePath = getAbsolutePath(pathPart, currentRootLinePath);
      rootLine.push(currentRootLinePath);
    });
    return rootLine;
  });
};

export const getLevel = (path, currentPath) => getPathParts(path, currentPath).length;

export const isMetaData = (path, currentPath) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  return absolutePath === '/metaData' || absolutePath.startsWith('/metaData/');
};

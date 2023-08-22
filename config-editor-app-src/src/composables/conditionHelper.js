export const EVENT_CONDITION_EVALUATION = 'dmf-configuration-editor--condition-evaluation';

/**
 * example detail object:
 * {
 *   store: STORE_OBJECT,
 *   resolve: RESOLVE_FUNCTION,
 *   currentPath: '/some/path',
 *   type: 'not-empty',
 *   config: {
 *     'path': '/foo/bar'
 *   }
 * }
 */
document.addEventListener(EVENT_CONDITION_EVALUATION, (e) => {
  if (e.detail.type !== 'not-empty') {
    return;
  }
  const store = e.detail.store;
  const resolve = e.detail.resolve;
  const currentPath = e.detail.currentPath;
  const config = e.detail.config;
  const value = store.getValue(config.path, currentPath);
  resolve(value !== '');
});

/**
 * example detail object:
 * {
 *   store: STORE_OBJECT,
 *   resolve: RESOLVE_FUNCTION,
 *   currentPath: '/some/path',
 *   type: 'empty',
 *   config: {
 *     'path': '/foo/bar'
 *   }
 * }
 */
document.addEventListener(EVENT_CONDITION_EVALUATION, (e) => {
  if (e.detail.type !== 'empty') {
    return;
  }
  const store = e.detail.store;
  const resolve = e.detail.resolve;
  const currentPath = e.detail.currentPath;
  const config = e.detail.config;
  const value = store.getValue(config.path, currentPath);
  const result = value === '';
  resolve(result);
});

/**
 * example detail object:
 * {
 *   store: STORE_OBJECT,
 *   resolve: RESOLVE_FUNCTION,
 *   currentPath: '/some/path',
 *   type: 'equals',
 *   config: {
 *     'path': '/foo/bar',
 *     'value': 'someValue'
 *   }
 * }
 */
document.addEventListener(EVENT_CONDITION_EVALUATION, (e) => {
  if (e.detail.type !== 'equals') {
    return;
  }
  const store = e.detail.store;
  const resolve = e.detail.resolve;
  const currentPath = e.detail.currentPath;
  const config = e.detail.config;
  const value = store.getValue(config.path, currentPath);
  // we use non-strict comparison to not have to deal with type conversion
  resolve(config.value == value);
});

/**
 * example detail object:
 * {
 *   store: STORE_OBJECT,
 *   resolve: RESOLVE_FUNCTION,
 *   currentPath: '/some/path',
 *   type: 'in',
 *   config: {
 *     'path': '/foo/bar',
 *     'list': { VALUE_CONFIG }
 *   }
 * }
 */
document.addEventListener(EVENT_CONDITION_EVALUATION, (e) => {
  if (e.detail.type !== 'in') {
    return;
  }
  const store = e.detail.store;
  const resolve = e.detail.resolve;
  const currentPath = e.detail.currentPath;
  const config = e.detail.config;
  const value = store.getValue(config.path, currentPath);
  const list = store.getPredefinedValues(config.list, currentPath);
  let result = false;
  for (let key in list) {
    // we use non-strict comparison to not have to deal with type conversion
    if (key == value) {
      result = true;
      break;
    }
  }
  resolve(result);
});

/**
 * example detail object:
 * {
 *   store: STORE_OBJECT,
 *   resolve: RESOLVE_FUNCTION,
 *   currentPath: '/some/path',
 *   type: 'not',
 *   config: { SUB_CONDITION_CONFIG }
 * }
 */
document.addEventListener(EVENT_CONDITION_EVALUATION, (e) => {
  if (e.detail.type !== 'not') {
    return;
  }
  const store = e.detail.store;
  const resolve = e.detail.resolve;
  const currentPath = e.detail.currentPath;
  const config = e.detail.config;
  const result = store.evaluateCondition(config, currentPath);
  resolve(!result);
});

/**
 * example detail object:
 * {
 *   store: STORE_OBJECT,
 *   resolve: RESOLVE_FUNCTION,
 *   currentPath: '/some/path',
 *   type: 'and',
 *   config: [
 *     SUB_CONDITION_CONFIG_1,
 *     SUB_CONDITION_CONFIG_2,
 *     SUB_CONDITION_CONFIG_3
 *   ]
 * }
 */
document.addEventListener(EVENT_CONDITION_EVALUATION, (e) => {
  if (e.detail.type !== 'and') {
    return;
  }
  const store = e.detail.store;
  const resolve = e.detail.resolve;
  const currentPath = e.detail.currentPath;
  const config = e.detail.config;
  let result = true;
  for (let index = 0; index < config.length; index++) {
    if (!store.evaluateCondition(config[index], currentPath)) {
      result = false;
      break;
    }
  }
  resolve(result);
});

/**
 * example detail object:
 * {
 *   store: STORE_OBJECT,
 *   resolve: RESOLVE_FUNCTION,
 *   currentPath: '/some/path',
 *   type: 'or',
 *   config: [
 *     SUB_CONDITION_CONFIG_1,
 *     SUB_CONDITION_CONFIG_2,
 *     SUB_CONDITION_CONFIG_3
 *   ]
 * }
 */
document.addEventListener(EVENT_CONDITION_EVALUATION, (e) => {
  if (e.detail.type !== 'or') {
    return;
  }
  const store = e.detail.store;
  const resolve = e.detail.resolve;
  const currentPath = e.detail.currentPath;
  const config = e.detail.config;
  let result = false;
  for (let index = 0; index < config.length; index++) {
    if (store.evaluateCondition(config[index], currentPath)) {
      result = true;
      break;
    }
  }
  resolve(result);
});

/**
 * example detail object (space between * and / is necessary to not close the block comment and should be ignored):
 * {
 *   store: STORE_OBJECT,
 *   resolve: RESOLVE_FUNCTION,
 *   currentPath: '/some/path',
 *   type: 'unique',
 *   config: {
 *     valuePath: 'foo',
 *     pathPattern: '../* /foo',
 *   }
 * }
 */
document.addEventListener(EVENT_CONDITION_EVALUATION, (e) => {
  if (e.detail.type !== 'unique') {
    return;
  }
  const store = e.detail.store;
  const resolve = e.detail.resolve;
  const currentPath = e.detail.currentPath;
  const config = e.detail.config;
  const valuePath = store.getAbsolutePath(config.valuePath, currentPath);
  const value = store.getValue(valuePath);
  const pathPattern = config.pathPattern;
  let paths = store.getAllPaths(pathPattern, currentPath);
  let result = true;
  for (let index = 0; index < paths.length; index++) {
    const path = paths[index];
    if (path !== valuePath && store.getValue(path) === value) {
      result = false;
      break;
    }
  }
  resolve(result);
});

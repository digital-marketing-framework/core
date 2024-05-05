import { getAbsolutePath } from '@/helpers/path';
import { isContainerType, isScalarType } from '@/helpers/type';
import { useDmfStore } from '@/stores/dmf';
import { useConditions } from './conditions';
import { useLabelProcessor } from './label';
import { WARNING_DOCUMENT_INVALID, useNotifications } from './notifications';
import { usePathProcessor } from './path';
import { useValueSets } from './valueSets';

const _validateAllowedValues = (store, schema, value, currentPath) => {
  if (isScalarType(schema.type) && schema.allowedValues) {
    const { getPredefinedValues } = useValueSets(store);
    const allowedValueLabelPairs = getPredefinedValues(schema.allowedValues, currentPath);
    const allowedValues = Object.keys(allowedValueLabelPairs);
    if (allowedValues.length > 0 && allowedValues.indexOf(value) === -1) {
      return 'value "' + value + '" is not allowed';
    }
  }
  return '';
};

const _validateSchemaType = (store, schema, value) => {
  switch (schema.type) {
    case 'CONTAINER': {
      if (typeof value !== 'object') {
        return schema.type.toLowerCase() + ' value must be an object';
      }
      if (Array.isArray(value)) {
        return schema.type.toLowerCase() + ' value must not be an array';
      }
      break;
    }
    case 'SWITCH': {
      if (typeof value !== 'object') {
        return schema.type.toLowerCase() + ' value must be an object';
      }
      if (Array.isArray(value)) {
        return schema.type.toLowerCase() + ' value must not be an array';
      }
      // TODO check switch-specific structures
      break;
    }
    case 'LIST': {
      if (typeof value !== 'object') {
        return schema.type.toLowerCase() + ' value must be an object';
      }
      if (Array.isArray(value)) {
        return schema.type.toLowerCase() + ' value must not be an array';
      }
      // TODO check list-specific structures
      break;
    }
    case 'MAP': {
      if (typeof value !== 'object') {
        return schema.type.toLowerCase() + ' value must be an object';
      }
      if (Array.isArray(value)) {
        return schema.type.toLowerCase() + ' value must not be an array';
      }
      // TODO check map-specific structures
      break;
    }
    case 'STRING': {
      if (typeof value !== 'string') {
        return 'string value must be a string';
      }
      break;
    }
    case 'BOOLEAN': {
      if (typeof value !== 'boolean') {
        return 'boolean value must be a boolean';
      }
      break;
    }
    case 'INTEGER': {
      if (typeof value !== 'number') {
        return 'integer value must be a number';
      }
      break;
    }
    default: {
      const customSchema = store.getCustomSchema(schema.type);
      return _validateSchemaType(store, customSchema, value);
    }
  }
  return '';
};

const _processValidations = (store, path, validations) => {
  if (!validations) {
    return '';
  }
  const { evaluate } = useConditions(store);
  for (let index = 0; index < validations.length; index++) {
    if (!evaluate(validations[index]['condition'], path)) {
      return validations[index]['message'];
    }
  }
  return '';
};

const _processNecessaryValidations = (store, schema, path) =>
  _processValidations(store, path, schema.strictValidations);

const _processOptionalValidations = (store, schema, path) =>
  _processValidations(store, path, schema.validations);

const useOptionalValidations = (store) =>
  store.data.metaData.strictValidation || !store.settings.globalDocument;

const _validateSchemaWithoutChildren = (store, schema, value, path) => {
  let issue;

  issue = _validateSchemaType(store, schema, value);
  if (issue) {
    return issue;
  }

  issue = _validateAllowedValues(store, schema, value, path);
  if (issue) {
    return issue;
  }

  issue = _processNecessaryValidations(store, schema, path);
  if (issue) {
    return issue;
  }

  if (useOptionalValidations(store)) {
    issue = _processOptionalValidations(store, schema, path);
    if (issue) {
      return issue;
    }
  }

  return '';
};

const clearIssues = (store, path, currentPath) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  Object.keys(store.issues).forEach((key) => {
    if (key.startsWith(absolutePath)) {
      delete store.issues[key];
    }
  });
};

const skipValidation = (store, path, currentPath) => {
  if (!store.isVisible(path, currentPath)) {
    return true;
  }

  const { isOutboundRoutePath, isInboundRoutePath, isDataProviderPath } = usePathProcessor(store);

  const outboundRoute = isOutboundRoutePath(path, currentPath);
  if (outboundRoute) {
    return !store.data.integrations[outboundRoute.integration].outboundRoutes[outboundRoute.id].value.config[outboundRoute.keyword].enabled;
  }

  const inboundRoute = isInboundRoutePath(path, currentPath);
  if (inboundRoute) {
    return !store.data.integrations[inboundRoute.integration].inboundRoutes[inboundRoute.keyword].enabled;
  }

  const dataProvider = isDataProviderPath(path, currentPath);
  if (dataProvider) {
    return !store.data.dataProcessing.dataProviders[dataProvider.keyword].enabled;
  }

  return false;
};

const _validate = (store, path, currentPath) => {
  clearIssues(store, path, currentPath);
  if (skipValidation(store, path, currentPath)) {
    return;
  }
  const absolutePath = getAbsolutePath(path, currentPath);
  const schema = store.getSchema(path, currentPath, true);
  const value = store.getValue(path, currentPath);
  const issue = _validateSchemaWithoutChildren(store, schema, value, absolutePath);
  if (issue) {
    store.issues[absolutePath] = issue;
  } else if (isContainerType(schema.type)) {
    const { getChildPaths } = usePathProcessor(store);
    const childPaths = getChildPaths(path, currentPath);
    childPaths.forEach((key) => {
      _validate(store, key, absolutePath);
    });
  }
};

// evaluate(path, currentPath) {
const validate = (store, path, currentPath) => {
  // TODO if there is an issue reported with a path, should the corresponding component go into raw mode automatically?
  //      probably not a good idea for all issues, like a required field being empty
  //      but it might be good for issues where the data structure is invalid
  _validate(store, path, currentPath);
  updateValidationWarnings(store);
};

const updateValidationWarnings = (store) => {
  const issueKeys = Object.keys(store.issues);
  const { setWarning, unsetWarning } = useNotifications(store);
  if (issueKeys.length > 0) {
    const issueKey = issueKeys[0];
    const { getLabel } = useLabelProcessor(store);
    const { selectPath } = usePathProcessor(store);
    setWarning(
      WARNING_DOCUMENT_INVALID,
      () => {
        selectPath(issueKey);
      },
      '[' + getLabel(issueKey) + '] ' + store.issues[issueKey]
    );
  } else {
    unsetWarning(WARNING_DOCUMENT_INVALID);
  }
};

const getIssue = (store, path, currentPath, recursive) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  if (!recursive) {
    return store.issues[absolutePath] || '';
  }
  const issueKeys = Object.keys(store.issues).sort();
  for (let index = 0; index < issueKeys.length; index++) {
    const key = issueKeys[index];
    if (key.startsWith(absolutePath)) {
      return store.issues[key];
    }
  }
  return '';
};

const hasIssues = (store, path, currentPath, recursive) =>
  getIssue(store, path, currentPath, recursive) !== '';

export const useValidation = (store) => {
  store = store || useDmfStore();
  return {
    validate: (path, currentPath) => validate(store, path, currentPath),
    updateValidationWarnings: () => updateValidationWarnings(store),

    clearIssues: (path, currentPath) => clearIssues(store, path, currentPath),
    getIssue: (path, currentPath, recursive) => getIssue(store, path, currentPath, recursive),
    hasIssues: (path, currentPath, recursive) => hasIssues(store, path, currentPath, recursive)
  };
};

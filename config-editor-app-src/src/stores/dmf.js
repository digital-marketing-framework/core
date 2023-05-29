import { defineStore } from 'pinia';

export const EVENT_APP_REQUEST = 'dmf-configuration-editor-app-request';
export const EVENT_APP_START = 'dmf-configuration-editor-app-start';

const NATIVE_SCHEMA_TYPES = [
    'SWITCH',
    'CONTAINER',
    'MAP',
    'LIST',
    'STRING',
    'INTEGER',
    'BOOLEAN',
];
const CONTAINER_TYPES = [
    'SWITCH',
    'CONTAINER',
    'MAP',
    'LIST'
];
const DYNAMIC_CONTAINER_TYPES = [
  'LIST',
  'MAP'
];

const doFetchData = async function() {
  const dataPromise = new Promise(resolve => {
    document.addEventListener(EVENT_APP_START, e => {
      resolve(e.detail);
    });
  });
  document.dispatchEvent(new Event(EVENT_APP_REQUEST));
  return dataPromise;
};

window.DMF_CONFIG_EDITOR = window.DMF_CONFIG_EDITOR || {
  data: {},
  inheritedData: {},
  schemaDocument: {
    valueSets: {},
    types: {},
    schema: {
      type: 'CONTAINER',
      values: []
    }
  },
  settings: {
  },
  onSave: null,
  onIncludeChange: null,
  loaded: false
};

export const useDmfStore = defineStore('dmf', {
  state: () => ({
      selectedPath: '/',
      rawViewPaths: {},
      collapsedMenuPaths: {},

      data: window.DMF_CONFIG_EDITOR.data,
      inheritedData: window.DMF_CONFIG_EDITOR.inheritedData,
      schemaDocument: window.DMF_CONFIG_EDITOR.schemaDocument,
      settings: window.DMF_CONFIG_EDITOR.settings,
      onSave: window.DMF_CONFIG_EDITOR.onSave,
      onIncludeChange: window.DMF_CONFIG_EDITOR.onIncludeChange,

      loaded: window.DMF_CONFIG_EDITOR.loaded,
      issues: {},
      messages: []
  }),
  actions: {
    writeMessage(message, type) {
      this.messages.push({text: message, type: type || 'info'});
    },
    _updateData(dataKey, data) {
      Object.keys(data).forEach(key => {
        this[dataKey][key] = data;
      });
    },
    async fetchData() {
      const response = await doFetchData();
      this._updateData('data', response.data);
      this._updateData('inheritedData', response.inheritedData);
      this._updateData('schemaDocument', response.inheritedDocument);
      this._updateData('settings', response.settings);
      this.onSave = response.onSave;
      this.onIncludeChange = response.onIncludeChange;
      this.loaded = true;
      this._updateValue('/');
      this.fix('/');
      this.evaluate('/');
      this.writeMessage('loaded!');
    },
    async save() {
      await this.onSave(this.data);
      this.writeMessage('saved!');
    },
    async updateIncludes() {
      const newData = await this.onIncludeChange(this.data);
      this.data = newData;
      this.writeMessage('includes updated!');
    },
    _updateParentValue(path, currentPath) {
      const parentPath = path + '/..';
      const parentValue = this.getValue(parentPath, currentPath);
      if (typeof parentValue === 'undefined') {
          this._updateParentValue(parentPath, currentPath);
          this.setValue(parentPath, currentPath, this.getDefaultValue(parentPath, currentPath));
      }
    },
    _updateValue(path, currentPath) {
      const value = this.getValue(path, currentPath);
      if (typeof value === 'undefined') {
          this.setValue(path, currentPath, this.getDefaultValue(path, currentPath));
      } else {
          const absolutePath = this.getAbsolutePath(path, currentPath);
          this.getChildPaths(path, currentPath).forEach(childPath => {
              this._updateValue(childPath, absolutePath);
          });
      }
    },
    processSwitchChange(path, currentPath, newValue) {
      const lastPathPart = this.getLeafKey(path, currentPath);
      if (lastPathPart !== 'type') {
          return;
      }
      const parentSchema = this.getSchema(path + '/..', currentPath, true);
      if (parentSchema.type !== 'SWITCH') {
          return;
      }
      const type = typeof newValue === 'undefined' ? this.getValue(path, currentPath, true) : newValue;
      this._updateValue(path + '/../config/' + type, currentPath);
    },
    setValue(path, currentPath, value, isSwitchKey) {
      if (this.isRoot(path, currentPath)) {
        this.data = value;
      } else {
        // this._updateParentValue(path, currentPath);
        if (isSwitchKey) {
          this.processSwitchChange(path, currentPath, value);
        }
        const parentPath = path + '/..';
        const lastPathPart = this.getLeafKey(path, currentPath);
        const parent = this.getValue(parentPath, currentPath);
        parent[lastPathPart] = value;
      }
    },
    updateMapKey(path, currentPath, key) {
      const parentPath = path + '/..';
      const schema = this.getSchema(parentPath, currentPath, true);
      if (schema.type !== 'MAP') {
          throw new Error('type ' + schema.type + ' does not have dynamic keys');
      }
      const lastPathPart = this.getLeafKey(path, currentPath);
      const map = this.getValue(parentPath, currentPath);
      // TODO this algorithm will change the position of the child element, can we change that?
      const mapItemValue = map[lastPathPart];
      delete map[lastPathPart];
      map[key] = mapItemValue;
    },
    _addValue(schema, path, currentPath) {
      switch (schema.type) {
        case 'LIST': {
            const list = this.getValue(path, currentPath, true);
            const defaultValue = this._getDefaultValue(schema.valueTemplate);
            const index = list.length;
            this.setValue(path + '/' + index, currentPath, defaultValue);
            break;
        }
        case 'MAP': {
            const defaultKey = this._getDefaultValue(schema.keyTemplate);
            const defaultValue = this._getDefaultValue(schema.valueTemplate);
            let keyCount = 0;
            let key = defaultKey;
            while (typeof this.getValue(path + '/' + key, currentPath) !== 'undefined') {
              keyCount++;
              key = defaultKey + '-' + keyCount;
            }
            this.setValue(path + '/' + key, currentPath, defaultValue);
            break;
        }
        case 'STRING':
        case 'INTEGER':
        case 'BOOLEAN':
        case 'SWITCH':
        case 'CONTAINER': {
            throw new Error('type ' + schema.type + ' does not have dynamic sub values');
        }
        default: {
            const customSchema = this.getCustomSchema(schema.type);
            this._addValue(customSchema, path, currentPath);
        }
    }
    },
    addValue(path, currentPath) {
      const schema = this.getSchema(path, currentPath);
      this._addValue(schema, path, currentPath);
    },
    removeValue(path, currentPath) {
      const parentPath = path + '/..';
      const parentSchema = this.getSchema(parentPath, currentPath, true);
      const lastPathPart = this.getLeafKey(path, currentPath);
      switch (parentSchema.type) {
          case 'SWITCH': {
              throw new Error('switch container elements can\'t be removed');
          }
          case 'CONTAINER': {
              throw new Error('container elements can\'t be removed');
          }
          case 'MAP': {
              const map = this.getValue(parentPath, currentPath);
              delete map[lastPathPart];
              break;
          }
          case 'LIST': {
              const list = this.getValue(parentPath, currentPath);
              list.splice(lastPathPart, 1);
              break;
          }
          default: {
              throw new Error('type ' + parentSchema.type + ' does not have sub elements');
          }
      }
    },
    resetValue(path, currentPath) {
      this.setValue(path, currentPath, this.getInheritedValue(path, currentPath));
    },
    selectPath(path, currentPath) {
      this.selectedPath = this.getAbsolutePath(path, currentPath);
    },
    selectParentPath() {
      if (!this.isRoot(this.selectedPath)) {
        this.selectedPath = this.getAbsolutePath('..', this.selectedPath);
      }
    },
    _evaluateAllowedValues(schema, value) {
      if (this.isScalarType(schema.type) && schema.allowedValues) {
          const allowedValues = this.getPredefinedValues(schema.allowedValues);
          if (allowedValues.length > 0 && allowedValues.indexOf(value) === -1) {
              return 'value "' + value + '" is not allowed';
          }
      }
      return '';
    },
    _evaluateSchemaType(schema, value) {
      switch (schema.type) {
          case 'SWITCH':
          case 'CONTAINER':
          case 'MAP': {
              if (typeof value !== 'object') {
                  return schema.type.toLowerCase() + ' value must be an object';
              }
              if (Array.isArray(value)) {
                  return schema.type.toLowerCase() + ' value must not be an array';
              }
              break;
          }
          case 'LIST': {
              if (typeof value !== 'object') {
                  return schema.type.toLowerCase() + ' value must be an object';
              }
              if (!Array.isArray(value)) {
                  return schema.type.toLowerCase() + ' value must be an array';
              }
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
              const customSchema = this.getCustomSchema(schema.type);
              return this._evaluateSchemaType(customSchema, value);
          }
      }
      return '';
    },
    _evaluateSchemaWithoutChildren(schema, value) {
      const SCHEMA_EVALUATIONS = ['_evaluateSchemaType', '_evaluateAllowedValues'];
      for (let index = 0; index < SCHEMA_EVALUATIONS.length; index++) {
          const issue = this[SCHEMA_EVALUATIONS[index]](schema, value);
          if (issue) {
              return issue;
          }
      }
      return '';
    },
    _evaluate(path, currentPath, issues) {
      issues = issues || [];
      const absolutePath = this.getAbsolutePath(path, currentPath);
      const schema = this.getSchema(path, currentPath, true);
      const value = this.getValue(path, currentPath);

      const issue = this._evaluateSchemaWithoutChildren(schema, value);
      if (issue) {
          issues.push(absolutePath + ': ' + issue);
      } else if (this.isContainerType(schema.type)) {
          const childPaths = this.getChildPaths(path, currentPath);
          childPaths.forEach(key => {
              if (schema.type === 'MAP') {
                  const issue = this._evaluateSchemaWithoutChildren(schema.keyTemplate, key);
                  if (issue) {
                      const absoluteChildPath = this.getAbsolutePath(key, absolutePath);
                      issues.push(absoluteChildPath + ': ' + issue);
                  }
              }
              this._evaluate(key, absolutePath, issues);
          });
      }
      return issues;
    },
    evaluate(path, currentPath) {
      this.issues = this._evaluate(path, currentPath);
      if (this.issues.length > 0) {
        console.log('issues', this.issues);
      }
    },
    fix(path, currentPath) {
      const schema = this.getSchema(path, currentPath);
      const value = this.getValue(path, currentPath);
      if (schema.type === 'MAP' && typeof value === 'object' && Array.isArray(value) && value.length === 0) {
        // empty map values can be interpreted as array instead of object by parsers
        this.setValue(path, currentPath, {});
      } else {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        this.getChildPaths(path, currentPath).forEach(childPath => {
          this.fix(childPath, absolutePath);
        });
      }
    },
    toggleView(path, currentPath) {
      const absolutePath = this.getAbsolutePath(path, currentPath);
      this.rawViewPaths[absolutePath] = !this.rawViewPaths[absolutePath];
    }
  },
  getters: {
    isNativeType() {
      return type => NATIVE_SCHEMA_TYPES.indexOf(type) >= 0;
    },
    isCustomType() {
      return type => !this.isNativeType(type);
    },
    isContainerType() {
      return type => CONTAINER_TYPES.indexOf(type) >= 0;
    },
    isDynamicContainerType() {
      return type => DYNAMIC_CONTAINER_TYPES.indexOf(type) >= 0;
    },
    isScalarType() {
      return type => !this.isContainerType(type);
    },
    isDynamicContainer() {
      return (path, currentPath) => this.isDynamicContainerType(this.getSchema(path, currentPath).type);
    },
    isDynamicChild() {
      return (path, currentPath) => !this.isRoot(path, currentPath) && this.isDynamicContainer(path + '/..', currentPath);
    },
    isRoot() {
      return (path, currentPath) => this.getLevel(path, currentPath) === 0;
    },
    getLevel() {
      return (path, currentPath) => this._getPathParts(path, currentPath).length;
    },
    _pathIsAbsolute() {
      return path => path.toString().startsWith('/');
    },
    _sanitizePath() {
      return path => {
        path = path.toString();
        if (path !== '/' && path.toString().endsWith('/')) {
            path = path.slice(0, -1);
        }
        path = path.replace('\\/\\/+', '\\/');
        return path;
      };
    },
    _simplifyPath() {
      return absolutePath => {
        absolutePath = this._sanitizePath(absolutePath);
        if (!this._pathIsAbsolute(absolutePath)) {
            throw new Error('path needs to be absolute');
        }
        const resultPathParts = [];
        const pathParts = absolutePath.substring(1).split('/');
        pathParts.forEach(pathPart => {
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
    },
    getAbsolutePath() {
      return (path, currentPath) => {
        path = this._sanitizePath(path);
        currentPath = this._sanitizePath(currentPath || '/');
        if (!path.startsWith('/')) {
            if (!this._pathIsAbsolute(currentPath)) {
                throw new Error('current path needs to be absolute');
            }
            path = currentPath === '/' ?  '/' + path : currentPath + '/' + path;
        }
        return this._simplifyPath(path);
      };
    },
    getLeafKey() {
      return (path, currentPath) => this.getAbsolutePath(path, currentPath).split('/').pop();
    },
    getParentPath() {
      return (path, currentPath) => this.getAbsolutePath('..', this.getAbsolutePath(path, currentPath));
    },
    _getPathParts() {
      return (path, currentPath) => {
        const pathPartsString = this.getAbsolutePath(path, currentPath).substring(1);
        if (pathPartsString === '') {
            return [];
        }
        return pathPartsString.split('/');
      };
    },
    getCustomSchema(state) {
      return type => {
        if (typeof state.schemaDocument.types[type] !== 'undefined') {
          return state.schemaDocument.types[type];
        }
        throw new Error('type ' + type + ' is unknown');
      };
    },
    getPredefinedValues(state) {
      return (valueConfig) => {
        let values = {};
        if (valueConfig.list) {
          Object.keys(valueConfig.list).forEach(key => {
            values[key] = valueConfig.list[key];
          });
        }
        if (valueConfig.sets) {
          valueConfig.sets.forEach(setName => {
            const set = state.schemaDocument.valueSets[setName] || {};
            Object.keys(set).forEach(key => {
              values[key] = set[key];
            });
          });
        }
        if (valueConfig.references) {
          valueConfig.references.forEach(reference => {
            this.getChildPaths(reference).forEach(childPath => {
              if (typeof values[childPath] === 'undefined') {
                values[childPath] = this.getLabel(childPath, reference);
              }
            });
          });
        }
        return values;
      };
    },
    _getValues() {
      return (schema, field) => {
        if (schema[field]) {
          return this.getPredefinedValues(schema[field]);
        }
        if (!this.isNativeType(schema.type)) {
          const customSchema = this.getCustomSchema(schema.type);
          return this._getValues(customSchema, field);
        }
        return {};
      };
    },
    _getAllowedValues() {
      return schema => this._getValues(schema, 'allowedValues');
    },
    getAllowedValues() {
      return (path, currentPath) => this._getAllowedValues(this.getSchema(path, currentPath));
    },
    _getFirstValue() {
      return (schema, field) => {
        const values = this._getValues(schema, field);
        const keys = Object.keys(values);
        if (keys.length > 0) {
          return keys[0];
        }
        return null;
      };
    },
    _getFirstValueLabel() {
      return (schema, field) => {
        const values = this._getValues(schema, field);
        const keys = Object.keys(values);
        if (keys.length > 0) {
          return values[keys[0]];
        }
        return null;
      };
    },
    _getFirstAllowedValue() {
      return schema => this._getFirstValue(schema, 'allowedValues');
    },
    getFirstAllowedValue() {
      return (path, currentPath) => this._getFirstAllowedValue(this.getSchema(path, currentPath));
    },
    _getFirstAllowedValueLabel() {
      return schema => this._getFirstValueLabel(schema, 'allowedValues');
    },
    getFirstAllowedValueLabel() {
      return (path, currentPath) => this._getFirstAllowedValueLabel(this.getSchema(path, currentPath));
    },
    _getSuggestedValues() {
      return schema => this._getValues(schema, 'suggestedValues');
    },
    getSuggestedValues() {
      return (path, currentPath) => this._getSuggestedValues(this.getSchema(path, currentPath));
    },
    _getFirstSuggestedValue() {
      return schema => this._getFirstValue(schema, 'suggestedValues');
    },
    getFirstSuggestedValue() {
      return (path, currentPath) => this._getFirstSuggestedValue(this.getSchema(path, currentPath));
    },
    _getFirstSuggestedValueLabel() {
      return schema => this._getFirstValueLabel(schema, 'suggestedValues');
    },
    getFirstSuggestedValueLabel() {
      return (path, currentPath) => this._getFirstSuggestedValueLabel(this.getSchema(path, currentPath));
    },
    _getDefaultValue() {
      return schema => {
        if (typeof schema.default !== 'undefined') {
          return schema.default;
        }
        const firstAllowedValue = this._getFirstAllowedValue(schema);
        if (firstAllowedValue !== null) {
          return firstAllowedValue;
        }
        const firstSuggestedValue = this._getFirstSuggestedValue(schema);
        if (firstSuggestedValue !== null) {
          return firstSuggestedValue;
        }
        switch (schema.type) {
          case 'CONTAINER': {
            const defaultValue = {};
            schema.values.forEach(subSchema => {
                defaultValue[subSchema.key] = this._getDefaultValue(subSchema);
            });
            return defaultValue;
          }
          case 'SWITCH': {
            const defaultValue = {};
            let defaultType = null;
            let configSchema = null;
            schema.values.forEach(subSchema => {
                switch (subSchema.key) {
                    case 'config': {
                        configSchema = subSchema;
                        break;
                    }
                    case 'type': {
                        defaultType = this._getDefaultValue(subSchema);
                        defaultValue.type = defaultType;
                        break;
                    }
                    default: {
                        defaultValue[subSchema.key] = this._getDefaultValue(subSchema);
                        break;
                    }
                }
              });
              if (defaultType === null) {
                  throw new Error('no "type" property found in switch schema');
              }
              if (configSchema === null) {
                  throw new Error('no "config" property found in switch schema');
              }
              defaultValue.config = {};
              configSchema.values.forEach(subSchema => {
                if (subSchema.key === defaultType) {
                  defaultValue.config[defaultType] = this._getDefaultValue(subSchema);
                }
              });
              return defaultValue;
          }
          case 'MAP': {
              return {};
          }
          case 'LIST': {
              return [];
          }
          case 'STRING': {
              return '';
          }
          case 'INTEGER': {
              return 0;
          }
          case 'BOOLEAN': {
              return false;
          }
          default: {
              const customSchema = this.getCustomSchema(schema.type);
              return this._getDefaultValue(customSchema);
          }
        }
      };
    },
    getDefaultValue() {
      return (path, currentPath) => {
        const schema = this.getSchema(path, currentPath);
        return this._getDefaultValue(schema);
      };
    },
    resolveSchema() {
      return schema => {
        if (this.isNativeType(schema.type)) {
          return schema;
        }
        schema = this.getCustomSchema(schema.type);
        return this.resolveSchema(schema);
      };
    },
    _getSchema() {
      return (pathParts, currentSchema) => {
        if (pathParts.length === 0) {
          return currentSchema;
      }

      const pathPart = pathParts.shift();
      switch (currentSchema.type) {
          case 'SWITCH':
          case 'CONTAINER': {
              for (let index in currentSchema.values) {
                  const subSchema = currentSchema.values[index];
                  if (subSchema.key === pathPart) {
                      return this._getSchema(pathParts, subSchema);
                  }
              }
              throw new Error('key ' + pathPart + ' not found in schema');
          }
          case 'MAP': {
              return this._getSchema(pathParts, currentSchema.valueTemplate);
          }
          case 'LIST': {
              if (!/^[0-9]+$/.test(pathPart)) {
                  throw new Error('list key "' + pathPart + '" is not numeric');
              }
              return this._getSchema(pathParts, currentSchema.valueTemplate);
          }
          case 'STRING':
          case 'INTEGER':
          case 'BOOLEAN': {
              throw new Error('scalar schema ' + currentSchema.type + ' does not have a sub schema');
          }
          default: {
              const customSchema = this.getCustomSchema(currentSchema.type);
              pathParts.unshift(pathPart);
              return this._getSchema(pathParts, customSchema);
          }
        }
      };
    },
    getSchema(state) {
      return (path, currentPath, resolveCustomType) => {
        const pathParts = this._getPathParts(path, currentPath);
        const schema = this._getSchema(pathParts, state.schemaDocument.schema, state.data);
        return resolveCustomType ? this.resolveSchema(schema) : schema;
      };
    },
    _getValue() {
      return (pathParts, currentValue) => {
        for (let index in pathParts) {
            const pathPart = pathParts[index];
            if (typeof currentValue[pathPart] === 'undefined') {
                return undefined;
            }
            currentValue = currentValue[pathPart];
        }
        return currentValue;
      };
    },
    getValue(state) {
      return (path, currentPath, withDefault, source) => {
        source = source || state.data;
        const pathParts = this._getPathParts(path, currentPath);
        const value = this._getValue(pathParts, source);
        if (typeof value !== 'undefined') {
            return value;
        }
        if (withDefault) {
            return this.getDefaultValue(path, currentPath);
        }
        return undefined;
      };
    },
    getInheritedValue(state) {
      return (path, currentPath, withDefault) => {
        return this.getValue(path, currentPath, withDefault, state.inheritedData);
      };
    },
    getParentValue() {
      return (path, currentPath, withDefault, source) => {
        if (this.isRoot(path, currentPath)) {
          return undefined;
        }
        return this.getValue(path + '/..', currentPath, withDefault, source);
      };
    },
    _getChildPaths() {
      return (schema, path, currentPath, absolute) => {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        switch (schema.type) {
            case 'SWITCH': {
                const paths = [];
                for (let index in schema.values) {
                    const childSchema = schema.values[index];
                    if (childSchema.key === 'config') {
                        const type = this.getValue(path + '/type', currentPath, true);
                        paths.push(absolute ? absolutePath + '/config/' + type : 'config/' + type);
                    } else {
                        paths.push(absolute ? absolutePath + '/' + childSchema.key : childSchema.key);
                    }
                }
                return paths;
            }
            case 'CONTAINER': {
                const paths = [];
                for (let index in schema.values) {
                    const childSchema = schema.values[index];
                    paths.push(absolute ? absolutePath + '/' + childSchema.key : childSchema.key);
                }
                return paths;
            }
            case 'MAP': {
                const map = this.getValue(path, currentPath, true);
                const paths = [];
                for (let key in map) {
                    paths.push(absolute ? absolutePath + '/' + key : key);
                }
                return paths;
            }
            case 'LIST': {
                const list = this.getValue(path, currentPath, true);
                const paths = [];
                for (let index = 0; index < list.length; index++) {
                    paths.push(absolute ? absolutePath + '/' + index : index);
                }
                return paths;
            }
            case 'STRING':
            case 'INTEGER':
            case 'BOOLEAN': {
                return [];
            }
            default: {
                const customSchema = this.getCustomSchema(schema.type);
                return this._getChildPaths(customSchema, path, currentPath);
            }
        }
      };
    },
    getChildPaths() {
      return (path, currentPath, absolute) => {
          const schema = this.getSchema(path, currentPath);
          const childPaths = this._getChildPaths(schema, path, currentPath, absolute);
          return childPaths;
      };
    },
    _isNavigationItem() {
      return schema => {
        if (typeof schema.navigationItem !== 'undefined') {
          return !!schema.navigationItem;
        }
        if (this.isCustomType(schema.type)) {
          return this._isNavigationItem(this.getCustomSchema(schema.type));
        }
        return this.isContainerType(schema.type);
      };
    },
    isNavigationItem() {
      return (path, currentPath) => this._isNavigationItem(this.getSchema(path, currentPath));
    },
    getNavigationChildPaths() {
      return (path, currentPath, absolute) => {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        const childPaths = this.getChildPaths(path, currentPath, absolute);
        return childPaths.filter(childPath => this.isNavigationItem(childPath, absolutePath));
      };
    },
    isSelected(state) {
      return (path, currentPath) => state.selectedPath === this.getAbsolutePath(path, currentPath);
    },
    getSelectedPath(state) {
      return () => state.selectedPath;
    },
    _valuesEqual() {
      return (a, b) => {
        if (typeof a === 'object' && typeof b === 'object') {
            for (let key in a) {
                if (typeof b[key] === 'undefined') {
                    return false;
                }
                if (!this._valuesEqual(a[key], b[key])) {
                    return false;
                }
            }
            return true;
        } else {
            return a === b;
        }
      };
    },
    isInherited() {
      return (path, currentPath) => {
        const value = this.getValue(path, currentPath);
        const inheritedValue = this.getInheritedValue(path, currentPath);
        if (typeof inheritedValue === 'undefined') {
            return false;
        }
        return this._valuesEqual(value, inheritedValue);
      };
    },
    isOverwritten() {
      return (path, currentPath) => {
        if (this.isInherited(path, currentPath)) {
            return false;
        }
        if (typeof this.getValue(path, currentPath) === 'undefined') {
            return false;
        }
        return true;
      };
    },
    getIssue(state) {
      return (path, currentPath) => {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        for (let index = 0; index < state.issues.length; index++) {
          const issue = state.issues[index];
          if (issue.startsWith(absolutePath)) {
            return issue.substring(absolutePath.length + 2);
          }
        }
        return '';
      };
    },
    hasIssues() {
      return (path, currentPath) => this.getIssue(path, currentPath) !== '';
    },
    getLabel() {
      return (path, currentPath) => {
        const ucfirst = s => s.substring(0, 1).toUpperCase() + s.substring(1);
        const schema = this.getSchema(path, currentPath);
        if (schema.hideLabel) {
          return '';
        }
        if (schema.label) {
          return schema.label;
        }
        const key = this.getLeafKey(path, currentPath);
        let label = key;
        label = label.replace(/[A-Z]+/g, function(match) { return ' ' + match; });
        label = label.replace(/[^a-zA-Z0-9]+([a-zA-Z0-9]+)/g, function(wholeMatch, match) { return ' ' + ucfirst(match); });
        return ucfirst(label);
      };
    },
    getRootLine() {
      return (path, currentPath) => {
        const pathParts = this._getPathParts(path, currentPath);
        let currentRootLinePath = '/';
        const rootLine = [];
        pathParts.forEach(pathPart => {
          currentRootLinePath = this.getAbsolutePath(pathPart, currentRootLinePath);
          rootLine.push(currentRootLinePath);
        });
        return rootLine;
      };
    },
    isRawView(state) {
      return (path, currentPath) => !!state.rawViewPaths[this.getAbsolutePath(path, currentPath)];
    },
    getItem() {
      return (path, currentPath) => {
        const schema = this.getSchema(path, currentPath, true);
        return {
            path: this.getAbsolutePath(path, currentPath),
            parentPath: this.isRoot(path, currentPath) ? '' : this.getParentPath(path, currentPath),
            value: this.getValue(path, currentPath, true),
            isRoot: this.isRoot(path, currentPath),
            rootLine: this.getRootLine(path, currentPath),
            schema: schema,
            level: this.getLevel(path, currentPath),
            currentKey: this.getLeafKey(path, currentPath),
            parentValue: this.getParentValue(path, currentPath),
            selected: this.isSelected(path, currentPath),
            isOverwritten: this.isOverwritten(path, currentPath),
            isScalar: this.isScalarType(schema.type),
            isContainer: this.isContainerType(schema.type),
            isDynamicContainer: this.isDynamicContainerType(schema.type),
            isDynamicItem: this.isDynamicChild(path, currentPath),
            childPaths: this.getChildPaths(path, currentPath),
            navigationChildPaths: this.getNavigationChildPaths(path, currentPath),
            label: this.getLabel(path, currentPath),
            hasIssues: this.hasIssues(path, currentPath),
            issue: this.getIssue(path, currentPath),
            rawView: this.isRawView(path, currentPath)
        };
      };
    },
    getParentItem() {
      return (path, currentPath) => this.isRoot(path, currentPath) ? undefined : this.getItem(this.getParentPath(path, currentPath));
    }
  }
});

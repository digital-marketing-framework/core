import { defineStore } from 'pinia';
// import { nextTick } from 'vue';
import { cloneValue, mergeValue, valuesEqual } from '../composables/valueHelper';

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

const WARNING_INCLUDES_CHANGED = 'includesChanged';
const WARNING_DOCUMENT_INVALID = 'documentInvalid';
const WARNINGS = {};
WARNINGS[WARNING_INCLUDES_CHANGED] = 'Includes have changed';
WARNINGS[WARNING_DOCUMENT_INVALID] = 'Document validation failed';

export const useDmfStore = defineStore('dmf', {
  state: () => ({
      selectedPath: '/',
      rawViewPaths: {},
      collapsedMenuPaths: {},
      collapsedContainerPaths: {},

      referenceIncludes: [],
      data: {},
      inheritedData: {},
      schemaDocument: {},
      settings: {},
      onSave: async () => {},
      onIncludeChange: async () => {},
      onClose: async () => {},

      loaded: false,
      isOpen: false,
      issues: {},
      warnings: {},
      messages: []
  }),
  actions: {
    writeMessage(message, type) {
      this.messages.push({text: message, type: type || 'info'});
      // this.triggerRerender();
    },
    removeMessage(index) {
      this.messages.splice(index, 1);
      // this.triggerRerender();
    },
    initData() {
      if (typeof this.data.metaData === 'undefined') {
        this.data.metaData = {};
      }
      if (typeof this.data.metaData.includes === 'undefined') {
        this.data.metaData.includes = [];
      }
      this.referenceIncludes = cloneValue(this.data.metaData.includes || []);
      this._updateValue('/');
      // this.evaluate('/');
      // this.triggerRerender();
    },
    async receiveData(response) {
      this.data = response.data;
      this.inheritedData = response.inheritedData;
      this.loaded = true;
      this.initData();
      // this.writeMessage('loaded!');
      // this.triggerRerender();
    },
    async open() {
      this.selectPath('/');
      this.isOpen = true;
      // this.triggerRerender();
    },
    async close() {
      this.isOpen = false;
      await this.onClose();
      // this.triggerRerender();
    },
    async save() {
      // TODO purge switch elements > do not delete, but reset the config items that are not selected
      // TODO check if includes have changed, updateIncludes() if they have
      this.finish('/');
      await this.onSave(this.data);
      // this.writeMessage('Document saved!');
    },
    async updateIncludes() {
      const response = await this.onIncludeChange(this.data);
      this.data = response.data;
      this.inheritedData = response.inheritedData;
      this.initData();
      this.unsetWarning(WARNING_INCLUDES_CHANGED);
      this.writeMessage('Includes updated successfully!');
    },
    // async triggerRerender() {
    //   // funny things I do that weirdly seem to help in some situations
    //   // to update all components when the state is changing
    //   window.dmfUpdateFunctionFwegwWfwegwFG = window.dmfUpdateFunctionFwegwWfwegwFG || function() {};
    //   window.dmfUpdateFunctionFwegwWfwegwFG(this.$forceUpdate);
    //   try {
    //     this.$forceUpdate();
    //   } catch(e) {
    //     // nothing to do here
    //   }
    //   // this.isOpen = false;
    //   // await nextTick();
    //   // this.isOpen = true;
    // },
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
      const parentSchema = this.getSchema('.', this.getParentPath(path, currentPath), true);
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
      if (this.getAbsolutePath(path, currentPath) === this.selectedPath) {
        this.selectParentPath();
      }
    },
    resetValue(path, currentPath) {
      const inheritedValue = this.getInheritedValue(path, currentPath);
      if (typeof inheritedValue === 'undefined') {
        this.removeValue(path, currentPath);
      } else {
        const clonedValue = cloneValue(inheritedValue);
        if (this.isRoot(path, currentPath)) {
          // do not reset meta data!
          const metaData = this.getValue('/metaData') || {};
          clonedValue.metaData = metaData;
        }
        this.setValue(path, currentPath, clonedValue);
      }
      // this.triggerRerender();
    },
    selectPath(path, currentPath) {
      this.selectedPath = this.getAbsolutePath(path, currentPath);
      // this.triggerRerender();
    },
    selectParentPath() {
      if (!this.isRoot(this.selectedPath)) {
        this.selectPath('..', this.selectedPath);
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
    setWarning(key, action, actionLabel) {
      const warning = {
        message: WARNINGS[key] || key
      };
      if (action) {
        warning.action = action;
      }
      if (actionLabel) {
        warning.actionLabel = actionLabel;
      }
      this.warnings[key] = warning;
    },
    unsetWarning(key) {
      if (typeof this.warnings[key] !== 'undefined') {
        delete this.warnings[key];
      }
    },
    _clearIssues(path, currentPath) {
      const absolutePath = this.getAbsolutePath(path, currentPath);
      if (typeof this.issues[absolutePath] !== 'undefined') {
        delete this.issues[absolutePath];
      }
    },
    _evaluate(path, currentPath) {
      this._clearIssues(path, currentPath);
      const absolutePath = this.getAbsolutePath(path, currentPath);
      const schema = this.getSchema(path, currentPath, true);
      const value = this.getValue(path, currentPath);
      const issue = this._evaluateSchemaWithoutChildren(schema, value);
      if (issue) {
          this.issues[absolutePath] = issue;
      } else if (this.isContainerType(schema.type)) {
          const childPaths = this.getChildPaths(path, currentPath);
          childPaths.forEach(key => {
              if (schema.type === 'MAP') {
                  const issue = this._evaluateSchemaWithoutChildren(schema.keyTemplate, key);
                  if (issue) {
                      const absoluteChildPath = this.getAbsolutePath(key, absolutePath);
                      this.issues[absoluteChildPath] = issue;
                  }
              }
              this._evaluate(key, absolutePath);
          });
      }
    },
    evaluate(path, currentPath) {
      // TODO trigger this action whenever something in the document changed
      // TODO if there is an issue reported with a path, should the corresponding component go into raw mode automatically?
      //      probably not a good idea for all issues, like a required field being empty
      //      but it might be good for issues where the data structure is invalid
      this._evaluate(path, currentPath);
      const issueKeys = Object.keys(this.issues);
      if (issueKeys.length > 0) {
        this.setWarning(WARNING_DOCUMENT_INVALID, null, issueKeys[0]);
      } else {
        this.unsetWarning(WARNING_DOCUMENT_INVALID);
      }
    },
    /**
     * This method does document cleanup tasks right before the document is saved.
     *
     * The SWITCH containers create data whenever their type is switched.
     * This creates a lot of overhead data that should stay during the editing
     * but that is useless when the document is about to be saved.
     *
     * TODO: Not implemented yet - we also may want to remove the artificially added include SYS:defaults.
     *
     * @param string path
     * @param string currentPath
     */
    finish(path, currentPath) {
      const schema = this.getSchema(path, currentPath, true);
      const absolutePath = this.getAbsolutePath(path, currentPath);
      if (schema.type === 'SWITCH') {
        const switchObject = this.getValue(path, currentPath);
        const selectedType = switchObject.type;
        Object.keys(switchObject.config).forEach(type => {
          if (selectedType !== type && !this.isInherited('config/' + type, absolutePath)) {
            delete switchObject.config[type];
          }
        });
      }
      this.getChildPaths(path, currentPath).forEach(childPath => {
        this.finish(childPath, absolutePath);
      });
    },
    toggleView(path, currentPath) {
      const absolutePath = this.getAbsolutePath(path, currentPath);
      this.rawViewPaths[absolutePath] = !this.rawViewPaths[absolutePath];
    },
    toggleContainerState(path, currentPath) {
      const absolutePath = this.getAbsolutePath(path, currentPath);
      this.collapsedContainerPaths[absolutePath] = !this.collapsedContainerPaths[absolutePath];
    },
    toggleContainerNavigationState(path, currentPath) {
      const absolutePath = this.getAbsolutePath(path, currentPath);
      this.collapsedMenuPaths[absolutePath] = !this.collapsedMenuPaths[absolutePath];
    }
  },
  getters: {
    getDocumentMetaData(state) {
      return () => state.data.metaData;
    },
    getDocumentName() {
      return () => this.getDocumentMetaData().name;
    },
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
      return (path, currentPath) =>
        this.isDynamicContainerType(this.getSchema(path, currentPath, true).type);
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
        const customSchema = cloneValue(this.getCustomSchema(schema.type));
        mergeValue(schema, customSchema, ['type']);
        return this.resolveSchema(customSchema);
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
        // TODO should we have the flag "withDefault"?
        //      or should we have another flag "addDefaultIfEmpty"
        //      or just do that automatically?
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
      return (path, currentPath, withDefault) => this.getValue(path, currentPath, withDefault, state.inheritedData);
    },
    getParentValue() {
      return (path, currentPath, withDefault, source) => {
        if (this.isRoot(path, currentPath)) {
          return undefined;
        }
        return this.getValue(path + '/..', currentPath, withDefault, source);
      };
    },
    getInheritedParentValue() {
      return (path, currentPath, withDefault) => this.getParentValue(path, currentPath, withDefault, this.inheritedData);
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
          const schema = this.getSchema(path, currentPath, true);
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
    isInherited() {
      return (path, currentPath) => {
        if (this.isMetaData(path, currentPath)) {
          return false;
        }
        const value = cloneValue(this.getValue(path, currentPath));
        const inheritedValue = cloneValue(this.getInheritedValue(path, currentPath));
        if (typeof inheritedValue === 'undefined') {
            return false;
        }
        if (this.isRoot(path, currentPath)) {
          if (typeof value.metaData !== 'undefined') {
            delete value.metaData;
          }
          if (typeof inheritedValue.metaData !== 'undefined') {
            delete inheritedValue.metaData;
          }
        }
        return valuesEqual(value, inheritedValue);
      };
    },
    isOverwritten() {
      return (path, currentPath) => {
        if (this.isMetaData(path, currentPath)) {
          return false;
        }
        if (this.isInherited(path, currentPath)) {
            return false;
        }
        if (typeof this.getValue(path, currentPath) === 'undefined') {
            return false;
        }
        // TODO if the inherited value does not exist at all, we must not count an element as overwritten if it is not dynamic
        //      it will have a parent element that is dynamic and is also overwritten. that's where the option to reset the element should be
        //      right now such a non-dynamic element is considered not overwritten, but it should probably still be marked as overwritten
        //      it should just not be possible to reset that particular element. maybe clicking the reset button should trigger the reset on the first dynamic parent element?
        if (typeof this.getInheritedValue(path, currentPath) === 'undefined' && !this.isDynamicChild(path, currentPath)) {
          return false;
        }
        return true;
      };
    },
    getIssue(state) {
      return (path, currentPath) => state.issues[this.getAbsolutePath(path, currentPath)] || '';
    },
    hasIssues() {
      return (path, currentPath) => this.getIssue(path, currentPath) !== '';
    },
    _processLabel() {
      return (label, path, currentPath) => {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        let variableFound = true;
        while (variableFound) {
          variableFound = false;
          label = label.replace(/\{[^}]+\}/, match => {
            variableFound = true;
            const referencePath = match.substring(1, match.length - 1);
            return this.getValue(referencePath, absolutePath);
          });
        }
        return label;
      };
    },
    _prettifyLabel() {
      return label => {
        const ucfirst = s => s.substring(0, 1).toUpperCase() + s.substring(1);
        label = label.replace(/[A-Z]+/g, match => ' ' + match);
        label = label.replace(/[^a-zA-Z0-9]+([a-zA-Z0-9]+)/g, (wholeMatch, match) => ' ' + ucfirst(match));
        label = label.replace(/[^a-zA-Z0-9]+$/, '');
        return ucfirst(label);
      };
    },
    getLabel() {
      return (path, currentPath) => {
        const schema = this.getSchema(path, currentPath, true);

        let label;
        if (schema.hideLabel) {
          label = '';
        } else if (schema.label) {
          label = this._processLabel(schema.label, path, currentPath);
        } else {
          label = this.getLeafKey(path, currentPath);
        }

        if (schema.useOriginalLabel) {
          return label;
        }

        return this._prettifyLabel(label);
      };
    },
    getRootLine() {
      return (path, currentPath) => {
        const pathParts = this._getPathParts(path, currentPath);
        let currentRootLinePath = '/';
        const rootLine = ['/'];
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
    isMetaData() {
      return (path, currentPath) => {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        return absolutePath === '/metaData' || absolutePath.startsWith('/metaData/');
      };
    },
    includesChanged(state) {
      return () => {
        const changed =JSON.stringify(state.data.metaData.includes || []) !== JSON.stringify(state.referenceIncludes || []);
        if (changed) {
          this.setWarning(WARNING_INCLUDES_CHANGED, () => { this.updateIncludes(); }, 'apply changes');
        } else {
          this.unsetWarning(WARNING_INCLUDES_CHANGED);
        }
        return changed;
      };
    },
    getContainerState(state) {
      return (path, currentPath) => {
        if (this.isSelected(path, currentPath)) {
          return true;
        }
        const absolutePath = this.getAbsolutePath(path, currentPath);
        if (typeof state.collapsedContainerPaths[absolutePath] === 'undefined') {
          const schema = this.getSchema(path, currentPath);
          // TODO setup schema rendering property "closedInitially", also, should it be closed initially or open initially?
          state.collapsedContainerPaths[absolutePath] = this.getSelectedPath() === absolutePath || (schema.openInitially ? true : false);
        }
        return state.collapsedContainerPaths[absolutePath];
      };
    },
    getContainerNavigationState(state) {
      return (path, currentPath) => {
        if (this.isRoot(path, currentPath)) {
          return true;
        }
        const absolutePath = this.getAbsolutePath(path, currentPath);
        if (typeof state.collapsedMenuPaths[absolutePath] === 'undefined') {
          // TODO should the default nav state be open or closed or configurable in schema?
          state.collapsedMenuPaths[absolutePath] = false;
        }
        return state.collapsedMenuPaths[absolutePath];
      };
    },
    getTriggers() {
      return (path, currentPath) => {
        return this.getSchema(path, currentPath, true).triggers || [];
      };
    },
    getItem() {
      return (path, currentPath) => {
        const schema = this.getSchema(path, currentPath, true);
        const immediateSchema = this.getSchema(path, currentPath);
        return {
            path: this.getAbsolutePath(path, currentPath),
            parentPath: this.isRoot(path, currentPath) ? '' : this.getParentPath(path, currentPath),
            value: this.getValue(path, currentPath, true),
            isRoot: this.isRoot(path, currentPath),
            rootLine: this.getRootLine(path, currentPath),
            schema: schema,
            immediateSchema: immediateSchema,
            custom: this.isCustomType(immediateSchema.type),
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
            rawView: this.isRawView(path, currentPath),
            triggers: this.getTriggers(path, currentPath)
        };
      };
    },
    getParentItem() {
      return (path, currentPath) => this.isRoot(path, currentPath) ? undefined : this.getItem(this.getParentPath(path, currentPath));
    }
  }
});

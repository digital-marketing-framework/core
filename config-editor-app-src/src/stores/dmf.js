import { defineStore } from 'pinia';
import { nextTick } from 'vue';
import { watch } from 'vue';
import { cloneValue, mergeValue, valuesEqual, EVENT_GET_VALUES } from '../composables/valueHelper';
import { EVENT_CONDITION_EVALUATION } from '../composables/conditionHelper';
import { rawDataParse, rawDataDump } from '../composables/rawValueHelper';
import { ListUtility } from '../composables/listValueHelper';
import { MapUtility } from '../composables/mapValueHelper';

const UUID_PLACEHOLDER = 'NEW';

const NATIVE_SCHEMA_TYPES = ['SWITCH', 'CONTAINER', 'MAP', 'LIST', 'STRING', 'INTEGER', 'BOOLEAN'];

const CONTAINER_TYPES = ['SWITCH', 'CONTAINER', 'MAP', 'LIST'];

const DYNAMIC_CONTAINER_TYPES = ['LIST', 'MAP'];

const WARNING_INCLUDES_CHANGED = 'includesChanged';
const WARNING_DOCUMENT_INVALID = 'documentInvalid';
const WARNINGS = {};
WARNINGS[WARNING_INCLUDES_CHANGED] = 'Includes have changed';
WARNINGS[WARNING_DOCUMENT_INVALID] = 'Document validation failed';

export const useDmfStore = defineStore('dmf', {
  state: () => ({
    selectedPath: '/',
    rawViewPaths: {},
    rawValues: {},
    rawIssues: {},
    collapsedMenuPaths: {},
    collapsedContainerPaths: {},

    referenceIncludes: {},
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
      this.messages.push({ text: message, type: type || 'info' });
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
        this.data.metaData.includes = {};
      }
      this.referenceIncludes = cloneValue(this.data.metaData.includes || {});
      this._updateValue('/');
      this.evaluate('/');
      watch(this.data, () => {
        this.evaluate('/');
      });
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
    async triggerRerender() {
      this.isOpen = false;
      await nextTick();
      this.isOpen = true;
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
        this.getChildPaths(path, currentPath).forEach((childPath) => {
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
      const type =
        typeof newValue === 'undefined' ? this.getValue(path, currentPath, true) : newValue;
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
        this.evaluate(path, currentPath);
      }
    },
    setRawValue(path, currentPath, value) {
      const currentValue = this.getValue(path, currentPath);
      try {
        const language = this.settings.rawLanguage;
        const dataFromString = rawDataParse(language, value);
        this.setValue(path, currentPath, dataFromString);

        // TODO we should distinguish between diffrent types of errors and not all of them should abort this process
        //      - soft validation errors for parent documents > should be ignored here
        //      - strict validation errors for all documents > should be ignored here
        //      - structure violations > should be taken into account and abort the process
        this.evaluate(path, currentPath);
        const issue = this.getIssue(path, currentPath, true);
        if (issue !== '') {
          throw new Error(issue);
        }

        this.unsetRawIssue(path, currentPath);
      } catch (e) {
        this.setValue(path, currentPath, currentValue);
        this.setRawIssue(path, currentPath, e.message);
      }
    },
    moveValueUp(path, currentPath) {
      if (!this.isDynamicChild(path, currentPath)) {
        throw new Error('cannot move non-dynamic items');
      }
      const containerUtility = this.getContainerUtility(this.getParentPath(path, currentPath));
      const list = this.getParentValue(path, currentPath);
      const item = this.getValue(path, currentPath);
      const previousItem = containerUtility.findPredecessor(list, containerUtility.getItemId(item));
      if (previousItem !== null) {
        containerUtility.moveBefore(
          list,
          containerUtility.getItemId(item),
          containerUtility.getItemId(previousItem)
        );
      }
    },
    moveValueDown(path, currentPath) {
      if (!this.isDynamicChild(path, currentPath)) {
        throw new Error('cannot move non-dynamic items');
      }
      const containerUtility = this.getContainerUtility(this.getParentPath(path, currentPath));
      const list = this.getParentValue(path, currentPath);
      const item = this.getValue(path, currentPath);
      const nextItem = containerUtility.findSuccessor(list, containerUtility.getItemId(item));
      if (nextItem !== null) {
        containerUtility.moveAfter(
          list,
          containerUtility.getItemId(item),
          containerUtility.getItemId(nextItem)
        );
      }
    },
    addValue(path, currentPath) {
      const absolutePath = this.getAbsolutePath(path, currentPath);
      if (!this.isDynamicContainer(path, currentPath)) {
        throw new Error(absolutePath + ': cannot add items to a non-dynamic container');
      }
      const schema = this.getSchema(path, currentPath, true);
      const container = this.getValue(path, currentPath, true);
      if (schema.type === 'LIST') {
        const defaultValue = this.getDefaultValue(
          UUID_PLACEHOLDER + '/' + ListUtility.KEY_VALUE,
          absolutePath,
          true
        );
        ListUtility.append(container, defaultValue);
      } else if (schema.type === 'MAP') {
        const defaultValue = this.getDefaultValue(
          UUID_PLACEHOLDER + '/' + MapUtility.KEY_VALUE,
          absolutePath,
          true
        );
        const defaultKey = this.getDefaultValue(
          UUID_PLACEHOLDER + '/' + MapUtility.KEY_KEY,
          absolutePath
        );
        MapUtility.append(container, defaultKey, defaultValue);
      }
      this.expandContainer(path, currentPath);
    },
    removeValue(path, currentPath) {
      if (!this.isDynamicChild(path, currentPath)) {
        throw new Error('non-dynamic items cannot be removed');
      }
      const container = this.getParentValue(path, currentPath);
      const item = this.getValue(path, currentPath);
      const containerUtility = this.getContainerUtility(this.getParentPath(path, currentPath));
      containerUtility.remove(container, containerUtility.getItemId(item));
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
      this.selectedPath = this.getClosestSelectablePath(path, currentPath);
      // TODO if requested path is not the selected path, scroll to the corresponding field. how?
      // this.triggerRerender();
    },
    selectParentPath() {
      if (!this.isRoot(this.selectedPath)) {
        this.selectPath('..', this.selectedPath);
      }
    },
    _evaluateAllowedValues(schema, value, currentPath) {
      if (this.isScalarType(schema.type) && schema.allowedValues) {
        const allowedValues = this.getPredefinedValues(schema.allowedValues, currentPath);
        if (allowedValues.length > 0 && allowedValues.indexOf(value) === -1) {
          return 'value "' + value + '" is not allowed';
        }
      }
      return '';
    },
    _evaluateSchemaType(schema, value) {
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
          const customSchema = this.getCustomSchema(schema.type);
          return this._evaluateSchemaType(customSchema, value);
        }
      }
      return '';
    },
    _processValidations(path, validations) {
      if (!validations) {
        return '';
      }
      for (let index = 0; index < validations.length; index++) {
        if (!this.evaluateCondition(validations[index]['condition'], path)) {
          return validations[index]['message'];
        }
      }
      return '';
    },
    _processStrictValidations(schema, path) {
      return this._processValidations(path, schema.strictValidations);
    },
    _processSoftValidations(schema, path) {
      return this._processValidations(path, schema.validations);
    },
    _evaluateSchemaWithoutChildren(schema, value, path) {
      let issue;

      issue = this._evaluateSchemaType(schema, value);
      if (issue) {
        return issue;
      }

      issue = this._evaluateAllowedValues(schema, value, path);
      if (issue) {
        return issue;
      }

      issue = this._processStrictValidations(schema, path);
      if (issue) {
        return issue;
      }

      if (!this.settings.globalDocument) {
        issue = this._processSoftValidations(schema, path);
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
      Object.keys(this.issues).forEach((key) => {
        if (key.startsWith(absolutePath)) {
          delete this.issues[key];
        }
      });
    },
    _evaluate(path, currentPath) {
      this._clearIssues(path, currentPath);
      const absolutePath = this.getAbsolutePath(path, currentPath);
      const schema = this.getSchema(path, currentPath, true);
      const value = this.getValue(path, currentPath);
      const issue = this._evaluateSchemaWithoutChildren(schema, value, absolutePath);
      if (issue) {
        this.issues[absolutePath] = issue;
      } else if (this.isContainerType(schema.type)) {
        const childPaths = this.getChildPaths(path, currentPath);
        childPaths.forEach((key) => {
          this._evaluate(key, absolutePath);
        });
      }
    },
    updateValidationWarnings() {
      const issueKeys = Object.keys(this.issues);
      if (issueKeys.length > 0) {
        const issueKey = issueKeys[0];
        this.setWarning(
          WARNING_DOCUMENT_INVALID,
          () => {
            this.selectPath(issueKey);
          },
          '[' + this.getLabel(issueKey) + '] ' + this.issues[issueKey]
        );
      } else {
        this.unsetWarning(WARNING_DOCUMENT_INVALID);
      }
    },
    evaluate(path, currentPath) {
      // TODO if there is an issue reported with a path, should the corresponding component go into raw mode automatically?
      //      probably not a good idea for all issues, like a required field being empty
      //      but it might be good for issues where the data structure is invalid
      this._evaluate(path, currentPath);
      this.updateValidationWarnings();
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
        Object.keys(switchObject.config).forEach((type) => {
          if (selectedType !== type && !this.isInherited('config/' + type, absolutePath)) {
            delete switchObject.config[type];
          }
        });
      }
      this.getChildPaths(path, currentPath).forEach((childPath) => {
        this.finish(childPath, absolutePath);
      });
    },
    getRawIssue(path, currentPath) {
      return this.rawIssues[this.getAbsolutePath(path, currentPath)] || '';
    },
    setRawIssue(path, currentPath, issue) {
      if (typeof issue === 'undefined') {
        delete this.rawIssues[this.getAbsolutePath(path, currentPath)];
      } else {
        this.rawIssues[this.getAbsolutePath(path, currentPath)] = issue;
      }
    },
    unsetRawIssue(path, currentPath) {
      this.setRawIssue(path, currentPath, undefined);
    },
    toggleView(path, currentPath) {
      const absolutePath = this.getAbsolutePath(path, currentPath);
      this.unsetRawIssue(path, currentPath);
      delete this.rawValues[absolutePath];
      this.rawViewPaths[absolutePath] = !this.rawViewPaths[absolutePath];
    },
    expandContainer(path, currentPath) {
      // TODO a changed container state is not taken into account immediately
      //      it is only read when the component is re-rendered
      //      that is also why the opening animation is missing
      //      how to open the Disclosure thingy properly?
      this.setContainerState(path, currentPath, true);
      this.triggerRerender();
    },
    setContainerState(path, currentPath, open) {
      const absolutePath = this.getAbsolutePath(path, currentPath);
      this.collapsedContainerPaths[absolutePath] = open;
    },
    toggleContainerState(path, currentPath) {
      const open = this.getContainerState(path, currentPath);
      this.setContainerState(path, currentPath, !open);
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
      return (type) => NATIVE_SCHEMA_TYPES.indexOf(type) >= 0;
    },
    isCustomType() {
      return (type) => !this.isNativeType(type);
    },
    isContainerType() {
      return (type) => CONTAINER_TYPES.indexOf(type) >= 0;
    },
    isDynamicContainerType() {
      return (type) => DYNAMIC_CONTAINER_TYPES.indexOf(type) >= 0;
    },
    isScalarType() {
      return (type) => !this.isContainerType(type);
    },
    isDynamicContainer() {
      return (path, currentPath) =>
        this.isDynamicContainerType(this.getSchema(path, currentPath, true).type);
    },
    isDynamicChild() {
      return (path, currentPath) =>
        !this.isRoot(path, currentPath) &&
        this.isDynamicContainer(this.getParentPath(path, currentPath));
    },
    isFirstDynamicChild() {
      return (path, currentPath) => {
        if (!this.isDynamicChild(path, currentPath)) {
          throw new Error('first item check not possible on non-dynamic items');
        }
        const item = this.getValue(path, currentPath);
        const list = this.getParentValue(path, currentPath);
        return ListUtility.isFirst(list, item[ListUtility.KEY_UID]);
      };
    },
    isLastDynamicChild() {
      return (path, currentPath) => {
        if (!this.isDynamicChild(path, currentPath)) {
          throw new Error('last item check not possible on non-dynamic items');
        }
        const item = this.getValue(path, currentPath);
        const list = this.getParentValue(path, currentPath);
        return ListUtility.isLast(list, item[ListUtility.KEY_UID]);
      };
    },
    isRoot() {
      return (path, currentPath) => this.getLevel(path, currentPath) === 0;
    },
    getLevel() {
      return (path, currentPath) => this._getPathParts(path, currentPath).length;
    },
    _pathIsAbsolute() {
      return (path) => path.toString().startsWith('/');
    },
    _sanitizePath() {
      return (path) => {
        path = path.toString();
        if (path !== '/' && path.toString().endsWith('/')) {
          path = path.slice(0, -1);
        }
        path = path.replace('\\/\\/+', '\\/');
        return path;
      };
    },
    _simplifyPath() {
      return (absolutePath) => {
        absolutePath = this._sanitizePath(absolutePath);
        if (!this._pathIsAbsolute(absolutePath)) {
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
    },
    getAbsolutePath() {
      return (path, currentPath) => {
        path = this._sanitizePath(path);
        currentPath = this._sanitizePath(currentPath || '/');
        if (!path.startsWith('/')) {
          if (!this._pathIsAbsolute(currentPath)) {
            throw new Error('current path needs to be absolute');
          }
          path = currentPath === '/' ? '/' + path : currentPath + '/' + path;
        }
        return this._simplifyPath(path);
      };
    },
    getAllPaths() {
      return (pathPattern, currentPath) => {
        pathPattern = this.getAbsolutePath(pathPattern, currentPath);
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
                this.getChildPaths(path).forEach((childPath) => {
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
    },
    getLeafKey() {
      return (path, currentPath) => this.getAbsolutePath(path, currentPath).split('/').pop();
    },
    getParentPath() {
      return (path, currentPath) =>
        this.getAbsolutePath('..', this.getAbsolutePath(path, currentPath));
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
      return (type) => {
        if (typeof state.schemaDocument.types[type] !== 'undefined') {
          return state.schemaDocument.types[type];
        }
        throw new Error('type ' + type + ' is unknown');
      };
    },
    evaluateCondition() {
      return (conditionConfig, currentPath) => {
        conditionConfig = cloneValue(conditionConfig);
        let result = false;
        conditionConfig.store = this;
        conditionConfig.currentPath = currentPath;
        conditionConfig.resolve = (_result) => {
          result = _result;
        };
        const e = new CustomEvent(EVENT_CONDITION_EVALUATION, {
          detail: conditionConfig
        });
        document.dispatchEvent(e);
        return result;
      };
    },
    getPredefinedValues() {
      return (valueConfig, currentPath) => {
        let values = {};
        Object.keys(valueConfig).forEach((keyword) => {
          const e = new CustomEvent(EVENT_GET_VALUES, {
            detail: {
              type: keyword,
              config: valueConfig[keyword],
              store: this,
              path: currentPath,
              add: (value, label) => {
                if (typeof values[value] === 'undefined') {
                  if (typeof label === 'undefined') {
                    label = value;
                  }
                  values[value] = label;
                }
              }
            }
          });
          document.dispatchEvent(e);
        });
        return values;
      };
    },
    _getValues() {
      return (schema, field, currentPath) => {
        if (schema[field]) {
          return this.getPredefinedValues(schema[field], currentPath);
        }
        if (!this.isNativeType(schema.type)) {
          const customSchema = this.getCustomSchema(schema.type);
          return this._getValues(customSchema, field, currentPath);
        }
        return {};
      };
    },
    _getAllowedValues() {
      return (schema, currentPath) => this._getValues(schema, 'allowedValues', currentPath);
    },
    getAllowedValues() {
      return (path, currentPath) =>
        this._getAllowedValues(
          this.getSchema(path, currentPath),
          this.getAbsolutePath(path, currentPath)
        );
    },
    _getFirstValue() {
      return (schema, field, currentPath) => {
        const values = this._getValues(schema, field, currentPath);
        const keys = Object.keys(values);
        if (keys.length > 0) {
          return keys[0];
        }
        return null;
      };
    },
    _getFirstValueLabel() {
      return (schema, field, currentPath) => {
        const values = this._getValues(schema, field, currentPath);
        const keys = Object.keys(values);
        if (keys.length > 0) {
          return values[keys[0]];
        }
        return null;
      };
    },
    _getFirstAllowedValue() {
      return (schema, currentPath) => this._getFirstValue(schema, 'allowedValues', currentPath);
    },
    getFirstAllowedValue() {
      return (path, currentPath) =>
        this._getFirstAllowedValue(
          this.getSchema(path, currentPath),
          this.getAbsolutePath(path, currentPath)
        );
    },
    _getFirstAllowedValueLabel() {
      return (schema, currentPath) =>
        this._getFirstValueLabel(schema, 'allowedValues', currentPath);
    },
    getFirstAllowedValueLabel() {
      return (path, currentPath) =>
        this._getFirstAllowedValueLabel(
          this.getSchema(path, currentPath),
          this.getAbsolutePath(path, currentPath)
        );
    },
    _getSuggestedValues() {
      return (schema, currentPath) => this._getValues(schema, 'suggestedValues', currentPath);
    },
    getSuggestedValues() {
      return (path, currentPath) =>
        this._getSuggestedValues(
          this.getSchema(path, currentPath),
          this.getAbsolutePath(path, currentPath)
        );
    },
    _getFirstSuggestedValue() {
      return (schema, currentPath) => this._getFirstValue(schema, 'suggestedValues', currentPath);
    },
    getFirstSuggestedValue() {
      return (path, currentPath) =>
        this._getFirstSuggestedValue(
          this.getSchema(path, currentPath),
          this.getAbsolutePath(path, currentPath)
        );
    },
    _getFirstSuggestedValueLabel() {
      return (schema, currentPath) =>
        this._getFirstValueLabel(schema, 'suggestedValues', currentPath);
    },
    getFirstSuggestedValueLabel() {
      return (path, currentPath) =>
        this._getFirstSuggestedValueLabel(
          this.getSchema(path, currentPath),
          this.getAbsolutePath(path, currentPath)
        );
    },
    _getDefaultValue() {
      return (schema, path, staticDefault) => {
        schema = this.resolveSchema(schema);

        if (typeof staticDefault === 'undefined') {
          staticDefault = null;
        }
        if (staticDefault === null) {
          if (typeof schema.default !== 'undefined') {
            staticDefault = cloneValue(schema.default);
          }
        }
        if (staticDefault === null) {
          const firstAllowedValue = this._getFirstAllowedValue(schema, path);
          if (firstAllowedValue !== null) {
            staticDefault = firstAllowedValue;
          }
        }
        if (staticDefault === null) {
          const firstSuggestedValue = this._getFirstSuggestedValue(schema, path);
          if (firstSuggestedValue !== null) {
            staticDefault = firstSuggestedValue;
          }
        }

        switch (schema.type) {
          case 'LIST': {
            if (staticDefault === null) {
              return {};
            }
            let valueSchema = null;
            schema.itemTemplate.values.forEach((itemSubSchema) => {
              if (itemSubSchema.key === ListUtility.KEY_VALUE) {
                valueSchema = itemSubSchema;
              }
            });
            if (valueSchema === null) {
              throw new Error('no list item value schema found');
            }
            const list = {};
            for (let index in staticDefault) {
              ListUtility.append(
                list,
                this._getDefaultValue(
                  valueSchema,
                  this.getAbsolutePath(UUID_PLACEHOLDER + '/' + ListUtility.KEY_VALUE, path),
                  staticDefault[index]
                )
              );
            }
            return list;
          }
          case 'MAP': {
            if (staticDefault === null) {
              return {};
            }
            let valueSchema = null;
            schema.itemTemplate.values.forEach((itemSubSchema) => {
              if (itemSubSchema.key === MapUtility.KEY_VALUE) {
                valueSchema = itemSubSchema;
              }
            });
            if (valueSchema === null) {
              throw new Error('no map item value schema found');
            }
            const map = {};
            for (let key in staticDefault) {
              MapUtility.append(
                map,
                key,
                this._getDefaultValue(
                  valueSchema,
                  this.getAbsolutePath(UUID_PLACEHOLDER + '/' + MapUtility.KEY_VALUE, path),
                  staticDefault[key]
                )
              );
            }
            return map;
          }
          case 'CONTAINER': {
            let defaultValue = {};
            if (staticDefault !== null) {
              defaultValue = staticDefault;
            }
            schema.values.forEach((subSchema) => {
              defaultValue[subSchema.key] = this._getDefaultValue(
                subSchema,
                this.getAbsolutePath(subSchema.key, path),
                defaultValue[subSchema.key]
              );
            });
            return defaultValue;
          }
          case 'SWITCH': {
            let defaultValue = {};
            if (staticDefault !== null) {
              defaultValue = staticDefault;
            }
            let defaultType = null;
            let configSchema = null;
            schema.values.forEach((subSchema) => {
              switch (subSchema.key) {
                case 'config': {
                  configSchema = subSchema;
                  break;
                }
                case 'type': {
                  defaultType = this._getDefaultValue(
                    subSchema,
                    this.getAbsolutePath('type', path),
                    defaultValue.type
                  );
                  defaultValue.type = defaultType;
                  break;
                }
                default: {
                  defaultValue[subSchema.key] = this._getDefaultValue(
                    subSchema,
                    this.getAbsolutePath(subSchema.key, path),
                    defaultValue[subSchema.key]
                  );
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
            defaultValue.config = defaultValue.config || {};
            configSchema.values.forEach((subSchema) => {
              if (subSchema.key === defaultType) {
                defaultValue.config[defaultType] = this._getDefaultValue(
                  subSchema,
                  this.getAbsolutePath('config/' + defaultType, path),
                  defaultValue.config[defaultType]
                );
              }
            });
            return defaultValue;
          }
          case 'STRING': {
            if (staticDefault !== null) {
              return staticDefault;
            }
            return '';
          }
          case 'INTEGER': {
            if (staticDefault !== null) {
              return staticDefault;
            }
            return 0;
          }
          case 'BOOLEAN': {
            if (staticDefault !== null) {
              return staticDefault;
            }
            return false;
          }
          default: {
            throw new Error('unknown schema type ' + schema.type);
          }
        }
      };
    },
    getDefaultValue() {
      return (path, currentPath) => {
        const schema = this.getSchema(path, currentPath, true);
        return this._getDefaultValue(schema, this.getAbsolutePath(path, currentPath));
      };
    },
    resolveSchema() {
      return (schema) => {
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
            return this._getSchema(pathParts, currentSchema.itemTemplate);
          }
          case 'LIST': {
            return this._getSchema(pathParts, currentSchema.itemTemplate);
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
      return (path, currentPath, withDefault) =>
        this.getValue(path, currentPath, withDefault, state.inheritedData);
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
      return (path, currentPath, withDefault) =>
        this.getParentValue(path, currentPath, withDefault, this.inheritedData);
    },
    getParentSchema() {
      return (path, currentPath, resolveCustomType) => {
        if (this.isRoot(path, currentPath)) {
          return undefined;
        }
        return this.getSchema(path + '/..' + currentPath, resolveCustomType);
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
            const values = schema.values.sort(
              (childSchema1, childSchema2) => childSchema1.weight - childSchema2.weight
            );
            values.forEach((childSchema) => {
              paths.push(absolute ? absolutePath + '/' + childSchema.key : childSchema.key);
            });
            return paths;
          }
          case 'MAP': {
            const map = this.getValue(path, currentPath, true);
            const pathPrefix = absolute ? absolutePath + '/' : '';
            const paths = [];
            Object.keys(MapUtility.sort(map)).forEach((id) => {
              paths.push(pathPrefix + id);
            });
            return paths;
          }
          case 'LIST': {
            const list = this.getValue(path, currentPath, true);
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
    getChildPathsGrouped() {
      return (path, currentPath, absolute) => {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        const childPaths = this.getChildPaths(path, currentPath, absolute);
        const result = {};
        childPaths.forEach((childPath) => {
          const schema = this.getSchema(childPath, absolutePath, true);
          const group = typeof schema.group !== 'undefined' ? schema.group : 'global';
          if (typeof result[group] === 'undefined') {
            result[group] = [];
          }
          result[group].push(childPath);
        });
        return result;
      };
    },
    _skipHeader() {
      return (schema) => {
        if (schema.skipHeader) {
          return true;
        }
        if (this.isCustomType(schema.type)) {
          return this._skipHeader(this.getCustomSchema(schema.type));
        }
        return false;
      };
    },
    skipHeader() {
      return (path, currentPath) => this._skipHeader(this.getSchema(path, currentPath));
    },
    _skipInNavigation() {
      return (schema) => {
        if (typeof schema.skipInNavigation !== 'undefined') {
          return schema.skipInNavigation;
        }
        if (this.isCustomType(schema.type)) {
          return this._skipInNavigation(this.getCustomSchema(schema.type));
        }
        return false;
      };
    },
    skipInNavigation() {
      return (path, currentPath) => this._skipInNavigation(this.getSchema(path, currentPath));
    },
    _isNavigationItem() {
      return (schema) => {
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
        const navigationChildPaths = [];
        childPaths.forEach((childPath) => {
          if (this.isNavigationItem(childPath, absolutePath)) {
            if (this.skipInNavigation(childPath, absolutePath)) {
              let childChildPaths = this.getNavigationChildPaths(childPath, absolutePath, absolute);
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
    canResetOverwrite() {
      return (path, currentPath) => {
        const isOverwritten = this.isOverwritten(path, currentPath);
        const inheritedValueExists =
          typeof this.getInheritedValue(path, currentPath) !== 'undefined';
        const isDynamicChildElement = this.isDynamicChild(path, currentPath);
        return isOverwritten && (inheritedValueExists || isDynamicChildElement);
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
        return true;
      };
    },
    getIssue(state) {
      return (path, currentPath, recursive) => {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        if (!recursive) {
          return state.issues[absolutePath] || '';
        }
        const issueKeys = Object.keys(state.issues).sort();
        for (let index = 0; index < issueKeys.length; index++) {
          const key = issueKeys[index];
          if (key.startsWith(absolutePath)) {
            return state.issues[key];
          }
        }
        return '';
      };
    },
    hasIssues() {
      return (path, currentPath, recursive) => this.getIssue(path, currentPath, recursive) !== '';
    },
    _processLabel() {
      return (label, path, currentPath) => {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        let anyVariableFound = false;
        let variableFound = true;
        while (variableFound) {
          variableFound = false;
          label = label.replace(/\{[^}]+\}/, (match) => {
            variableFound = true;
            anyVariableFound = true;
            const referencePath = match.substring(1, match.length - 1);
            return this.getValue(referencePath, absolutePath);
          });
        }
        return anyVariableFound ? this._prettifyLabel(label) : label;
      };
    },
    _prettifyLabel() {
      return (label) => {
        const ucfirst = (s) => s.substring(0, 1).toUpperCase() + s.substring(1);
        label = label.replace(/[A-Z]+/g, (match) => ' ' + match);
        label = label.replace(
          /[^a-zA-Z0-9]+([a-zA-Z0-9]+)/g,
          (wholeMatch, match) => ' ' + ucfirst(match)
        );
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
          label = schema.label;
          if (!schema.keepOriginalLabel) {
            label = this._processLabel(label, path, currentPath);
          }
        } else {
          label = this._prettifyLabel(this.getLeafKey(path, currentPath));
        }

        return label;
      };
    },
    getRootLine() {
      return (path, currentPath) => {
        const pathParts = this._getPathParts(path, currentPath);
        let currentRootLinePath = '/';
        const rootLine = ['/'];
        pathParts.forEach((pathPart) => {
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
        const changed = !ListUtility.equals(
          state.data.metaData.includes || {},
          state.referenceIncludes || {}
        );
        if (changed) {
          this.setWarning(
            WARNING_INCLUDES_CHANGED,
            () => {
              this.updateIncludes();
            },
            'apply changes'
          );
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
          state.collapsedContainerPaths[absolutePath] =
            this.getSelectedPath() === absolutePath || (schema.openInitially ? true : false);
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
    isVisible() {
      return (path, currentPath) => {
        const schema = this.getSchema(path, currentPath, true);
        if (!schema.visibility) {
          return true;
        }
        return this.evaluateCondition(schema.visibility, this.getAbsolutePath(path, currentPath));
      };
    },
    getRawValue() {
      return (path, currentPath) => {
        const value = this.getValue(path, currentPath, true);
        const language = this.settings.rawLanguage;
        return rawDataDump(language, value);
      };
    },
    _getContainerUtility() {
      return (schema) => {
        schema = this.resolveSchema(schema);
        switch (schema.type) {
          case 'LIST':
            return ListUtility;
          case 'MAP':
            return MapUtility;
        }
        throw new Error('schema ' + schema.type + ' is not a dynamic container');
      };
    },
    getContainerUtility() {
      return (path, currentPath) => this._getContainerUtility(this.getSchema(path, currentPath));
    },
    isPathSelectable() {
      return (path, currentPath) =>
        this.isNavigationItem(path, currentPath) && !this.skipInNavigation(path, currentPath);
    },
    getClosestSelectablePath() {
      return (path, currentPath) => {
        const absolutePath = this.getAbsolutePath(path, currentPath);
        const pathParts = absolutePath === '/' ? [] : absolutePath.split('/');
        let resultPath = '/';
        let nextPath = '/';
        while (pathParts.length > 0) {
          nextPath = this.getAbsolutePath(pathParts.shift(), nextPath);
          if (!this.isNavigationItem(nextPath)) {
            break;
          }
          if (!this.skipInNavigation(nextPath)) {
            resultPath = nextPath;
          }
        }
        return resultPath;
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
          canResetOverwrite: this.canResetOverwrite(path, currentPath),
          isScalar: this.isScalarType(schema.type),
          isContainer: this.isContainerType(schema.type),
          isDynamicContainer: this.isDynamicContainerType(schema.type),
          isDynamicItem: this.isDynamicChild(path, currentPath),
          childPaths: this.getChildPaths(path, currentPath),
          groupedChildPaths: this.getChildPathsGrouped(path, currentPath),
          navigationChildPaths: this.getNavigationChildPaths(path, currentPath),
          label: this.getLabel(path, currentPath),
          hasIssues: this.hasIssues(path, currentPath),
          issue: this.getIssue(path, currentPath),
          rawView: this.isRawView(path, currentPath),
          triggers: this.getTriggers(path, currentPath),
          isVisible: this.isVisible(path, currentPath)
        };
      };
    },
    getParentItem() {
      return (path, currentPath) =>
        this.isRoot(path, currentPath)
          ? undefined
          : this.getItem(this.getParentPath(path, currentPath));
    }
  }
});

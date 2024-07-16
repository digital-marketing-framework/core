import { defineStore } from 'pinia';
import { nextTick } from 'vue';
import { watch } from 'vue';
import { cloneValue, mergeValue, valuesEqual } from '@/helpers/value';
import { isNativeType } from '@/helpers/type';
import { debounce } from '@/utils/debounce';
import { cached } from '@/utils/processorCache';
import { isRoot, getAbsolutePath, getPathParts, getLeafKey, isMetaData } from '@/helpers/path';
import { usePathProcessor } from '@/composables/path';
import { useConditions } from '@/composables/conditions';
import { useDynamicProcessor } from '@/composables/dynamicItem';
import { useValidation } from '@/composables/validation';
import { useSwitch } from '@/composables/switch';
import { useDefaults } from '@/composables/defaults';

export const useDmfStore = defineStore('dmf', {
  state: () => ({
    // app state
    loaded: false,
    isOpen: false,

    // settings
    settings: {},

    // navigation and layout
    selectedPath: '/',
    rawViewPaths: {},
    rawValues: {},
    rawIssues: {},
    collapsedMenuPaths: {},
    collapsedContainerPaths: {},

    // data
    referenceIncludes: {},
    data: {},
    inheritedData: {},

    // schema
    schemaDocument: {},

    // notifications
    issues: {},
    warnings: {},
    messages: [],
    confirmDialog: {
      open: false,
      headline: '',
      text: '',
      yes: 'Yes',
      no: 'No',
      callback: null
    },

    // environment callbacks
    onSave: async () => {},
    onIncludeChange: async () => {},
    onClose: async () => {}
  }),
  actions: {
    // system
    async triggerRerender() {
      this.isOpen = false;
      await nextTick();
      this.isOpen = true;
    },

    // program flow
    initData() {
      if (typeof this.data.metaData === 'undefined') {
        this.data.metaData = {};
      }
      if (typeof this.data.metaData.includes === 'undefined') {
        this.data.metaData.includes = {};
      }
      this.referenceIncludes = cloneValue(this.data.metaData.includes || {});
      const { updateValue } = useDefaults(this);
      updateValue('/');
      const { validate } = useValidation(this);
      validate('/');
      watch(
        this.data,
        debounce(() => {
          validate('/');
        }, 300)
      );
      // this.triggerRerender();
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
      const { cleanupSwitchConfig } = useSwitch(this);
      cleanupSwitchConfig(path, currentPath);

      // TODO re-arrange list and map items so that their weight values are closer to their original state

      const { getChildPaths } = usePathProcessor(this);
      const absolutePath = getAbsolutePath(path, currentPath);
      getChildPaths(path, currentPath).forEach((childPath) => {
        this.finish(childPath, absolutePath);
      });
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
      const { selectPath } = usePathProcessor(this);
      selectPath('/');
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

    // value
    setValue(path, currentPath, value, isSwitchKey) {
      if (isRoot(path, currentPath)) {
        this.data = value;
      } else {
        if (isSwitchKey) {
          const { processSwitchChange } = useSwitch(this);
          processSwitchChange(path, currentPath, value);
        }
        const parentPath = path + '/..';
        const lastPathPart = getLeafKey(path, currentPath);
        const parent = this.getValue(parentPath, currentPath);
        parent[lastPathPart] = value;
        const { validate } = useValidation(this);
        validate(path, currentPath);
      }
    },

    // inherited value
    resetValue(path, currentPath) {
      const inheritedValue = this.getInheritedValue(path, currentPath);
      if (typeof inheritedValue === 'undefined') {
        const { removeValue } = useDynamicProcessor(this);
        removeValue(path, currentPath);
      } else {
        const clonedValue = cloneValue(inheritedValue);
        if (isRoot(path, currentPath)) {
          // do not reset meta data!
          const metaData = this.getValue('/metaData') || {};
          clonedValue.metaData = metaData;
        }
        this.setValue(path, currentPath, clonedValue);
      }
      // this.triggerRerender();
    }
  },
  getters: {
    // schema
    getCustomSchema(state) {
      return (type) => {
        if (typeof state.schemaDocument.types[type] !== 'undefined') {
          return state.schemaDocument.types[type];
        }
        throw new Error('type ' + type + ' is unknown');
      };
    },
    resolveSchema() {
      return (schema) => {
        if (isNativeType(schema.type)) {
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
        return cached('getSchema', [path, currentPath, resolveCustomType], () => {
          const pathParts = getPathParts(path, currentPath);
          const schema = this._getSchema(pathParts, state.schemaDocument.schema, state.data);
          return resolveCustomType ? this.resolveSchema(schema) : schema;
        });
      };
    },
    getParentSchema() {
      return (path, currentPath, resolveCustomType) => {
        if (isRoot(path, currentPath)) {
          return undefined;
        }
        return this.getSchema(path + '/..', currentPath, resolveCustomType);
      };
    },
    getSelectedSchema() {
      return (path, currentPath, schema) => {
        schema = this.resolveSchema(schema || this.getSchema(path, currentPath));
        if (schema.type === 'SWITCH') {
          const absolutePath = getAbsolutePath(path, currentPath);
          const selectedType = this.getValue('type', absolutePath);
          if (selectedType) {
            const selectedSchema = this.getSchema('config/' + selectedType, absolutePath);
            if (selectedSchema) {
              return selectedSchema;
            }
          }
        }
        return schema;
      };
    },

    // value
    getValue(state) {
      return (path, currentPath, withDefault, inherited) => {
        // TODO should we have the flag "withDefault"?
        //      or should we have another flag "addDefaultIfEmpty"
        //      or just do that automatically?

        let value = inherited ? state.inheritedData : state.data;
        const pathParts = getPathParts(path, currentPath);
        for (let index in pathParts) {
          const pathPart = pathParts[index];
          if (typeof value[pathPart] === 'undefined') {
            value = undefined;
            break;
          }
          value = value[pathPart];
        }

        if (typeof value !== 'undefined') {
          return value;
        }
        if (withDefault) {
          const { getDefaultValue } = useDefaults(this);
          return getDefaultValue(path, currentPath);
        }

        return undefined;
      };
    },
    getParentValue() {
      return (path, currentPath, withDefault, inherited) => {
        if (isRoot(path, currentPath)) {
          return undefined;
        }
        return this.getValue(path + '/..', currentPath, withDefault, inherited);
      };
    },

    // inherited value
    getInheritedValue() {
      return (path, currentPath, withDefault) =>
        this.getValue(path, currentPath, withDefault, true);
    },
    getInheritedParentValue() {
      return (path, currentPath, withDefault) =>
        this.getParentValue(path, currentPath, withDefault, true);
    },

    // misc
    // TODO move to composables?
    isInherited() {
      return (path, currentPath) => {
        if (isMetaData(path, currentPath)) {
          return false;
        }
        const value = cloneValue(this.getValue(path, currentPath));
        const inheritedValue = cloneValue(this.getInheritedValue(path, currentPath));
        if (typeof inheritedValue === 'undefined') {
          return false;
        }
        if (isRoot(path, currentPath)) {
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
        const { isDynamicChild } = useDynamicProcessor(this);
        const isOverwritten = this.isOverwritten(path, currentPath);
        const inheritedValueExists =
          typeof this.getInheritedValue(path, currentPath) !== 'undefined';
        const isDynamicChildElement = isDynamicChild(path, currentPath);
        return isOverwritten && (inheritedValueExists || isDynamicChildElement);
      };
    },
    isOverwritten() {
      return (path, currentPath) => {
        if (isMetaData(path, currentPath)) {
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
    getTriggers() {
      return (path, currentPath) => {
        return this.getSchema(path, currentPath, true).triggers || [];
      };
    },
    isVisible(state) {
      return (path, currentPath) => {
        const absolutePath = getAbsolutePath(path, currentPath);

        // the switch for soft validations is only available in global documents
        if (absolutePath === '/metaData/softValidation') {
          return state.settings.globalDocument;
        }

        const schema = this.getSchema(path, currentPath, true);
        if (!schema.visibility) {
          return true;
        }
        const { evaluate } = useConditions(this);
        return evaluate(schema.visibility, absolutePath);
      };
    }
  }
});

import { useFieldContextReference } from './fieldContextReference';
import { usePathProcessor } from '../path';

// *** data mapper group processor ***

const dataMapperProcessors = {
  fieldMap: (store, config, context, inputContext) => {
    for (let fieldItemId in config.fields) {
      const field = config.fields[fieldItemId].key;
      context[field] = [{ name: field, type: 'UNKNOWN' }];
    }
    return context;
  },
  passthroughFields: (store, config, context, inputContext) => {
    if (config.enabled) {
      for (let field in inputContext) {
        if (typeof context[field] === 'undefined') {
          context[field] = inputContext[field];
        }
      }
    }
    return context;
  },
  ignoreEmptyFields: (store, config, context, inputContext) => {
    // TODO
    return context;
  },
  excludeFields: (store, config, context, inputContext) => {
    // TODO
    return context;
  }
};

const dataMapperGroupProcessors = {
  sequence: (store, config, inputContext) => {
    let context = inputContext;
    for (let listItemId in config.list) {
      const subDataMapperGroupId = config.list[listItemId].value;
      context = processDataMapperGroup(store, subDataMapperGroupId, context);
    }
    return context;
  },
  single: (store, config, inputContext) => {
    inputContext = inputContext || {};
    let context = {};
    for (let dataMapperType in config.data) {
      const dataMapperProcessor = dataMapperProcessors[dataMapperType];
      if (!dataMapperProcessor) {
        continue;
      }
      context = dataMapperProcessor(store, config.data[dataMapperType], context, inputContext);
    }
    return context;
  }
};

// this kind of steam loop detection will stop working
// if we ever introduced parallel data mapper groups in addition to sequences
const dataMapperGroupLoopDetection = {};

const processDataMapperGroup = (store, dataMapperGroupId, inputContext) => {
  if (!dataMapperGroupId) {
    console.warn('empty data mapper group ID is invalid');
    return {};
  }

  if (dataMapperGroupLoopDetection[dataMapperGroupId]) {
    console.error('data mapper group loop detected', dataMapperGroupLoopDetection);
    return {};
  }
  dataMapperGroupLoopDetection[dataMapperGroupId] = true;

  const config = store.data.dataProcessing.dataMapperGroups[dataMapperGroupId].value;

  if (typeof inputContext === 'undefined') {
    const contextName = config.inputContext;
    inputContext = contextName ? getContext(store, contextName) : undefined;
  }

  const dataMapperGroupType = config.type;
  const dataMapperGroupConfig = config.config[dataMapperGroupType];

  const processor = dataMapperGroupProcessors[dataMapperGroupType];

  if (typeof processor === 'undefined') {
    console.error('unknown data mapper group type "' + dataMapperGroupType + '"');
    delete dataMapperGroupLoopDetection[dataMapperGroupId];
    return {};
  }

  let result = {};
  try {
    result = processor(store, dataMapperGroupConfig, inputContext);
  } catch (error) {
    console.error(error);
  }

  delete dataMapperGroupLoopDetection[dataMapperGroupId];
  return result;
};

// *** repository ***

const getStaticContextNames = (store, regexp) => {
  const names = [];
  for (let name in store.schemaDocument.fieldContexts || {}) {
    if (typeof regexp === 'undefined' || regexp.test(name)) {
      names.push(name);
    }
  }
  return names;
};

const combineContexts = (contexts) => {
  const result = {};
  contexts.forEach((context) => {
    for (let key in context) {
      if (typeof result[key] === 'undefined') {
        result[key] = [];
      }
      context[key].forEach((field) => {
        result[key].push(field);
      });
    }
  });
  return result;
};

const getCombinedContexts = (store, names) => {
  const contexts = [];
  names.forEach((name) => {
    contexts.push(getContext(store, name));
  });
  return combineContexts(contexts);
};

const staticContextExists = (store, name) => {
  return typeof store.schemaDocument.fieldContexts[name] !== 'undefined';
};

const getStaticContext = (store, name) => {
  if (!staticContextExists(store, name)) {
    return {};
  }
  const context = {};
  for (let key in store.schemaDocument.fieldContexts[name]) {
    const field = store.schemaDocument.fieldContexts[name][key];
    context[key] = [field];
  }
  return context;
};

// -- distributor --

const getDistributorInputDefaultsAllContext = (store) => {
  const names = getStaticContextNames(store, /^distributor\.in\.defaults\..+$/);
  const contexts = [];
  names.forEach((name) => {
    contexts.push(getStaticContext(store, name));
  });
  const additionalContext = getContext(store, 'distributor.in.dataProvider.all');
  contexts.push(additionalContext);
  return combineContexts(contexts);
};

const getDistributorInputDataProviderAllContext = (store) => {
  // TODO implement data provider context processing
  return {};
};

const getDistributorInputDefaultsContext = (store, name) => {
  const context = getStaticContext(store, name);
  const additionalContext = getContext(store, 'distributor.in.dataProvider.all');
  return combineContexts([context, additionalContext]);
};

const getDistributorOutputDefaultsContext = (store, name) => {
  return getStaticContext(store, name);
};

const getDistributorOutputContext = (store, name) => {
  // TODO
  return {};
};

// -- collector --

const getCollectorInputDefaultsContext = (store, name) => {
  return getStaticContext(store, name);
};

const getCollectorOutputAllContext = (store) => {
  const { getInboundRouteKeywords } = useFieldContextReference(store);
  const contexts = [];
  const keywords = getInboundRouteKeywords();
  for (let integration in keywords) {
    for (let keyword in keywords[integration]) {
      const context = getContext(store, 'collector.out.' + integration + '.' + keyword);
      contexts.push(context);
    }
  }
  return combineContexts(contexts);
};

const getCollectorOutputContext = (store, name) => {
  const [integration, keyword] = name.substring('collector.out.'.length).split('.');
  const collectorConfig = store.data.integrations[integration].inboundRoutes[keyword];
  const dataMapperGroupId = collectorConfig.dataMap;
  return processDataMapperGroup(store, dataMapperGroupId);
};

// -- data mapper group --

const getDataMapperGroupInputDefaultsContext = (store, name) => {
  const dataMapperGroupId = name.substring('dataMapperGroup.in.defaults.'.length);
  const contextName = store.data.dataProcessing.dataMapperGroups[dataMapperGroupId].value.inputContext;
  if (!contextName) {
    return {};
  }
  return getContext(store, contextName);
};

const getDataMapperGroupOutputDefaultsContext = (store, name) => {
  const dataMapperGroupId = name.substring('dataMapperGroup.out.defaults.'.length);
  const contextName = store.data.dataProcessing.dataMapperGroups[dataMapperGroupId].value.outputContext;
  if (!contextName) {
    return {};
  }
  return getContext(store, contextName);
};

const getDataMapperGroupOutputContext = (store, name) => {
  const dataMapperGroupId = name.substring('dataMapperGroup.out.'.length);
  return processDataMapperGroup(store, dataMapperGroupId);
};

// -- condition --

const getConditionInputDefaultsContext = (store, name) => {
  const conditionId = name.substring('condition.in.defaults.'.length);
  const contextName = store.data.dataProcessing.conditions[conditionId].value.inputContext;
  if (!contextName) {
    return {};
  }
  return getContext(store, contextName);
};

// -- content modifier --

const getContentModifierInputDefaultContext = (store, name) => {
  const contentModifierId = name.substring('personalization.contentModifiers.in.defaults.'.length);
  const contentModifierConfig = store.data.personalization.contentModifiers[contentModifierId].value;
  const contentModifierType = contentModifierConfig.type;
  const dataTransformationId = contentModifierConfig.config[contentModifierType].dataTransformationId;
  if (dataTransformationId === '') {
    return getCollectorOutputAllContext(store);
  }
  const dataMapperGroupId = store.data.personalization.dataTransformations[dataTransformationId].value.dataMap;
  if (dataMapperGroupId === '') {
    return getCollectorOutputAllContext(store);
  }
  return getDataMapperGroupOutputContext(store, 'dataMapperGroup.out.' + dataMapperGroupId);
};

/*
  distributor.in.defaults.EVENT.NAME
  distributor.in.defaults.all()
  distributor.in.dataProvider.all()
  distributor.out.defaults.INTEGRATION.ROUTE_KEYWORD
  distributor.out.INTEGRATION.ROUTE_ID()
  distributor.out.cache.cache()

  collector.in.defaults.INTEGRATION.ROUTE_KEYWORD
  collector.out.INTEGRATION.ROUTE_KEYWORD()
  collector.out.all()

  dataMapperGroup.in.defaults.ID
  dataMapperGroup.out.defaults.ID
  dataMapperGroup.out.ID()

  condition.in.defaults.ID
*/
const getContext = (store, name) => {
  if (name === '') {
    return {};
  }

  // -- distributor --
  if (name.startsWith('distributor.')) {
    if (name === 'distributor.in.defaults.all') {
      return getDistributorInputDefaultsAllContext(store);
    } else if (name === 'distributor.in.dataProvider.all') {
      return getDistributorInputDataProviderAllContext(store);
    } else if (name.startsWith('distributor.in.defaults.')) {
      return getDistributorInputDefaultsContext(store, name);
    } else if (name.startsWith('distributor.out.defaults.')) {
      return getDistributorOutputDefaultsContext(store, name);
    } else if (name.startsWith('distributor.out.')) {
      return getDistributorOutputContext(store, name);
    }
  } else if (name.startsWith('collector.')) {
    // -- collector --
    if (name.startsWith('collector.in.defaults.')) {
      return getCollectorInputDefaultsContext(store, name);
    } else if (name === 'collector.out.all') {
      return getCollectorOutputAllContext(store);
    } else if (name.startsWith('collector.out.')) {
      return getCollectorOutputContext(store, name);
    }
  } else if (name.startsWith('dataMapperGroup.')) {
    // -- data mapper group --
    if (name.startsWith('dataMapperGroup.in.defaults.')) {
      return getDataMapperGroupInputDefaultsContext(store, name);
    } else if (name.startsWith('dataMapperGroup.out.defaults.')) {
      return getDataMapperGroupOutputDefaultsContext(store, name);
    } else if (name.startsWith('dataMapperGroup.out.')) {
      return getDataMapperGroupOutputContext(store, name);
    }
  } else if (name.startsWith('condition.')) {
    // -- condition --
    if (name.startsWith('condition.in.defaults.')) {
      return getConditionInputDefaultsContext(store, name);
    }
  } else if (name.startsWith('personalization.')) {
    // -- personalization --
    if (name.startsWith('personalization.contentModifiers.in.defaults.')) {
      return getContentModifierInputDefaultContext(store, name);
    }
  }

  if (staticContextExists(store, name)) {
    console.warn('unknown field context "' + name + '", still loaded');
    return getStaticContext(store, name);
  }

  console.warn('unknown field context "' + name + '"');
  return {};
};

const getActiveInputContextNames = (store, path) => {
  const {
    isOutboundRoutePath,
    isInboundRoutePath,
    isDataMapperGroupPath,
    isConditionPath,
    isPersonalizationDataTransformationPath,
    isPersonalizationContentModifierPath
  } = usePathProcessor(store);

  const dataMapperGroupId = isDataMapperGroupPath(path);
  if (dataMapperGroupId) {
    return ['dataMapperGroup.in.defaults.' + dataMapperGroupId];
  }

  const conditionId = isConditionPath(path);
  if (conditionId) {
    return ['condition.in.defaults.' + conditionId];
  }

  if (isOutboundRoutePath(path)) {
    return ['distributor.in.defaults.all'];
  }

  const inboundRoute = isInboundRoutePath(path);
  if (inboundRoute) {
    return ['collector.in.defaults.' + inboundRoute.integration + '.' + inboundRoute.keyword];
  }

  if (isPersonalizationDataTransformationPath(path)) {
    return ['collector.out.all'];
  }

  const personalizationContentModifierId = isPersonalizationContentModifierPath(path);
  if (personalizationContentModifierId) {
    return ['personalization.contentModifiers.in.defaults.' + personalizationContentModifierId];
  }
  return [];
};

const getActiveOutputContextNames = (store, path) => {
  const { isOutboundRoutePath, isInboundRoutePath, isDataMapperGroupPath } = usePathProcessor(store);

  const dataMapperGroupId = isDataMapperGroupPath(path);
  if (dataMapperGroupId) {
    return ['dataMapperGroup.out.defaults.' + dataMapperGroupId];
  }

  const outboundRoute = isOutboundRoutePath(path);
  if (outboundRoute) {
    return ['distributor.out.defaults.' + outboundRoute.integration + '.' + outboundRoute.keyword];
  }

  const inboundRoute = isInboundRoutePath(path);
  if (inboundRoute) {
    // inbound routes do not have a default output context
    // return ['collector.out.defaults.' + inboundRouteKeyword];
  }

  return [];
};

const getActiveInputContext = (store, path) => {
  const contextNames = getActiveInputContextNames(store, path);
  return getCombinedContexts(store, contextNames);
};

const getActiveOutputContext = (store, path) => {
  const contextNames = getActiveOutputContextNames(store, path);
  return getCombinedContexts(store, contextNames);
};

const getInputContextNames = (store, path) => {
  const { getInboundRouteKeywords, getDataMapperGroupIds } = useFieldContextReference(store);
  const result = {};

  const inputIdentifier = store.settings.contextIdentifier || 'all';
  result['distributor.in.defaults.' + inputIdentifier] = 'Distributor Input';

  const collectorKeywords = getInboundRouteKeywords(store);
  for (let integration in collectorKeywords) {
    for (let key in collectorKeywords[integration]) {
      result['collector.in.defaults.' + integration + '.' + key] = 'Collector Input ' + (collectorKeywords[integration][key] || key);
    }
  }

  result['collector.out.all'] = 'Collector Output';

  const dataMapperGroupIds = getDataMapperGroupIds(store);
  for (let id in dataMapperGroupIds) {
    if (path.startsWith('/dataProcessing/dataMapperGroups/' + id + '/')) {
      continue;
    }
    result['dataMapperGroup.out.' + id] = 'Data Mapper Output ' + dataMapperGroupIds[id];
  }

  return result;
};

const getOutputContextNames = (store) => {
  const { getOutboundRouteKeywords } = useFieldContextReference(store);
  const result = {};

  const routeKeywords = getOutboundRouteKeywords(store);
  for (let integration in routeKeywords) {
    for (let key in routeKeywords[integration]) {
      result['distributor.out.defaults.' + integration + '.' + key] = 'Distributor Output ' + (routeKeywords[integration][key] || key);
    }
  }

  return result;
};

export const useFieldContextRepository = (store) => {
  return {
    getContext: (name) => getContext(store, name),

    getInputContextNames: (path) => getInputContextNames(store, path),
    getOutputContextNames: () => getOutputContextNames(store),

    getActiveInputContextNames: (path) => getActiveInputContextNames(store, path),
    getActiveInputContext: (path) => getActiveInputContext(store, path),
    getActiveOutputContextNames: (path) => getActiveOutputContextNames(store, path),
    getActiveOutputContext: (path) => getActiveOutputContext(store, path),

    processDataMapperGroup: (dataMapperGroupId) => processDataMapperGroup(store, dataMapperGroupId)
  };
};

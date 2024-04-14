import { useFieldContextReference } from './fieldContextReference';

// *** stream processor ***

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

const streamProcessors = {
  sequence: (store, config, inputContext) => {
    let context = inputContext;
    for (let listItemId in config.list) {
      const subStreamId = config.list[listItemId].value;
      context = processStream(store, subStreamId, context);
    }
    return context;
  },
  dataMapper: (store, config, inputContext) => {
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
// if we ever introduced parallel stream in addition to sequences
const streamLoopDetection = {};

const processStream = (store, streamId, inputContext) => {
  if (!streamId) {
    console.warn('empty stream ID is invalid');
    return {};
  }

  if (streamLoopDetection[streamId]) {
    console.error('stream loop detected', streamLoopDetection);
    return {};
  }
  streamLoopDetection[streamId] = true;

  const config = store.data.streams[streamId].value;

  if (typeof inputContext === 'undefined') {
    const contextName = config.inputContext;
    inputContext = contextName ? getContext(store, contextName) : undefined;
  }

  const streamType = config.type;
  const streamConfig = config.config[streamType];

  const processor = streamProcessors[streamType];

  if (typeof processor === 'undefined') {
    console.error('unknown stream type "' + streamType + '"');
    delete streamLoopDetection[streamId];
    return {};
  }

  let result = {};
  try {
    result = processor(store, streamConfig, inputContext);
  } catch (error) {
    console.error(error);
  }

  delete streamLoopDetection[streamId];
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
  // TODO
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
  const { getCollectorKeywords } = useFieldContextReference(store);
  const contexts = [];
  const keywords = getCollectorKeywords();
  for (let keyword in keywords) {
    const context = getContext(store, 'collector.out.' + keyword);
    contexts.push(context);
  }
  return combineContexts(contexts);
};

const getCollectorOutputContext = (store, name) => {
  const keyword = name.substring('collector.out.'.length);
  const collectorConfig = store.data.collector.collectors[keyword];
  const streamId = collectorConfig.dataMap;
  return processStream(store, streamId);
};

// -- stream --

const getStreamInputDefaultsContext = (store, name) => {
  const streamId = name.substring('stream.in.defaults.'.length);
  const contextName = store.data.streams[streamId].value.inputContext;
  if (!contextName) {
    return {};
  }
  return getContext(store, contextName);
};

const getStreamOutputDefaultsContext = (store, name) => {
  const streamId = name.substring('stream.out.defaults.'.length);
  const contextName = store.data.streams[streamId].value.outputContext;
  if (!contextName) {
    return {};
  }
  return getContext(store, contextName);
};

const getStreamOutputContext = (store, name) => {
  const streamId = name.substring('stream.out.'.length);
  return processStream(store, streamId);
};

// -- condition --

const getConditionInputDefaultsContext = (store, name) => {
  const evaluationId = name.substring('evaluation.in.defaults.'.length);
  const contextName = store.data.evaluations[evaluationId].value.inputContext;
  if (!contextName) {
    return {};
  }
  return getContext(store, contextName);
};

/*
  distributor.in.defaults.XYZ()
  distributor.in.defaults.all()
  distributor.in.dataProvider.all()
  distributor.out.defaults.XYZ
  distributor.out.XYZ#PQR()
  distributor.out.cache()

  collector.in.defaults.XYZ
  collector.out.XYZ()
  collector.out.all()

  stream.in.defaults.XYZ
  stream.out.defaults.XYZ
  stream.out.XYZ

  condition.in.defaults.XYZ
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
  } else if (name.startsWith('stream.')) {
    // -- stream --
    if (name.startsWith('stream.in.defaults.')) {
      return getStreamInputDefaultsContext(store, name);
    } else if (name.startsWith('stream.out.defaults.')) {
      return getStreamOutputDefaultsContext(store, name);
    } else if (name.startsWith('stream.out.')) {
      return getStreamOutputContext(store, name);
    }
  } else if (name.startsWith('evaluation.')) {
    // -- evaluation --
    if (name.startsWith('evaluation.in.defaults.')) {
      return getConditionInputDefaultsContext(store, name);
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
  if (path.startsWith('/streams/')) {
    const streamId = path.split('/')[2];
    return ['stream.in.defaults.' + streamId];
  } else if (path.startsWith('/evaluations/')) {
    const evaluationId = path.split('/')[2];
    return ['evaluation.in.defaults.' + evaluationId];
  } else if (path.startsWith('/distributor/')) {
    return ['distributor.in.defaults.all'];
  } else if (path.startsWith('/collector/collectors/')) {
    const collectorKeyword = path.split('/')[3];
    return ['collector.in.defaults.' + collectorKeyword];
  } else if (path.startsWith('/collector/')) {
    return ['collector.out.all'];
  }
  return [];
};

const getActiveOutputContextNames = (store, path) => {
  if (path.startsWith('/streams/')) {
    const streamId = path.split('/')[2];
    return ['stream.out.defaults.' + streamId];
  } else if (path.startsWith('/distributor/routes/')) {
    const routeId = path.split('/')[3];
    const type = store.data.distributor.routes[routeId].value.type;
    return ['distributor.out.defaults.' + type];
  } else if (path.startsWith('/collector/collectors/')) {
    // data collectors do not have a default output context
    // return ['collector.out.defaults.' + path.split('/')[3]];
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
  const { getCollectorKeywords, getStreamIds } = useFieldContextReference(store);
  const result = {};

  const inputIdentifier = store.settings.contextIdentifier || 'all';
  result['distributor.in.defaults.' + inputIdentifier] = 'Distributor Input';

  const collectorKeywords = getCollectorKeywords(store);
  for (let key in collectorKeywords) {
    result['collector.in.defaults.' + key] = 'Collector Input ' + (collectorKeywords[key] || key);
  }

  result['collector.out.all'] = 'Collector Output';

  const streamIds = getStreamIds(store);
  for (let id in streamIds) {
    if (path.startsWith('/streams/' + id + '/')) {
      continue;
    }
    result['stream.out.' + id] = 'Stream Output ' + streamIds[id];
  }

  return result;
};

const getOutputContextNames = (store) => {
  const { getRouteKeywords } = useFieldContextReference(store);
  const result = {};

  const routeKeywords = getRouteKeywords(store);
  for (let key in routeKeywords) {
    result['distributor.out.defaults.' + key] = 'Distributor Output ' + routeKeywords[key];
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

    processStream: (streamId) => processStream(store, streamId)
  };
};

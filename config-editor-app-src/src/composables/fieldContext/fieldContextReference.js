import { getAbsolutePath } from '@/helpers/path'

const getDataMapperGroupIds = (store) => {
  const ids = {};
  for (let id in store.data.dataProcessing.dataMapperGroups) {
    ids[id] = store.data.dataProcessing.dataMapperGroups[id].key;
  }
  return ids;
};

// const getRouteNames = (store) => {
//   const names = [];
//   for (let id in store.data.distributor.routes) {
//     const route = store.data.distributor.routes[id].value;
//     let name = route.type;
//     if (route.pass) {
//       name += '#' + route.pass;
//     }
//     names.push(name);
//   }
//   return names;
// };

const getIntegrationRouteKeywords = (store, direction) => {
  const result = {};
  for (let name in store.schemaDocument.valueSets) {
    const matches = name.match(new RegExp('^' + direction + '\\/([^/]+)\\/all$'));
    if (matches !== null) {
      const integration = matches[1];
      result[integration] = {};
      for (let key in store.schemaDocument.valueSets[name]) {
        result[integration][key] = store.schemaDocument.valueSets[name][key];
      }
    }
  }
  return result;
};

const getOutboundRouteKeywords = (store) => {
  return getIntegrationRouteKeywords(store, 'outboundRoutes');
};

const getInboundRouteKeywords = (store) => {
  return getIntegrationRouteKeywords(store, 'inboundRoutes');
};

export const useFieldContextReference = (store) => {
  return {
    getDataMapperGroupIds: () => getDataMapperGroupIds(store),
    getOutboundRouteKeywords: () => getOutboundRouteKeywords(store),
    // getRouteNames: () => getRouteNames(store),
    getInboundRouteKeywords: () => getInboundRouteKeywords(store)
  };
};

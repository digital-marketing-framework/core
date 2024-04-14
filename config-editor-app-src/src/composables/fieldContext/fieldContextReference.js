const getStreamIds = (store) => {
  const ids = {};
  for (let id in store.data.streams) {
    ids[id] = store.data.streams[id].key;
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

const getRouteKeywords = (store) => {
  return store.schemaDocument.valueSets['route/all'] || {};
};

const getCollectorKeywords = (store) => {
  return store.schemaDocument.valueSets['dataCollector/all'] || {};
};

export const useFieldContextReference = (store) => {
  return {
    getStreamIds: () => getStreamIds(store),
    getRouteKeywords: () => getRouteKeywords(store),
    // getRouteNames: () => getRouteNames(store),
    getCollectorKeywords: () => getCollectorKeywords(store)
  };
};

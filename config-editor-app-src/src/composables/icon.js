
const getIcon = (store, path, currentPath, schema) => {
  schema = store.resolveSchema(schema || store.getSchema(path, currentPath));
  const selectedSchema = store.getSelectedSchema(path, currentPath, schema);
  if (selectedSchema && selectedSchema.icon) {
    return selectedSchema.icon;
  }
  return schema.icon;
};

export const useIconProcessor = (store) => {
  return {
    getIcon: (path, currentPath, schema) => getIcon(store, path, currentPath, schema)
  };
};

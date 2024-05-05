const getRoles = (store, path) => {
  const schema = store.getSchema(path);
  return schema.roles || [];
};

const hasRole = (store, path, role) => getRoles(store, path).indexOf(role) >= 0;

export const useFieldRoles = (store) => {
  return {
    getRoles: (path) => getRoles(store, path),
    hasRole: (path, role) => hasRole(store, path, role)
  };
};

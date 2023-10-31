const NATIVE_SCHEMA_TYPES = ['SWITCH', 'CONTAINER', 'MAP', 'LIST', 'STRING', 'INTEGER', 'BOOLEAN'];
const CONTAINER_TYPES = ['SWITCH', 'CONTAINER', 'MAP', 'LIST'];
const DYNAMIC_CONTAINER_TYPES = ['LIST', 'MAP'];

export const isNativeType = (type) => NATIVE_SCHEMA_TYPES.indexOf(type) >= 0;

export const isCustomType = (type) => !isNativeType(type);

export const isContainerType = (type) => CONTAINER_TYPES.indexOf(type) >= 0;

export const isDynamicContainerType = (type) => DYNAMIC_CONTAINER_TYPES.indexOf(type) >= 0;

export const isScalarType = (type) => !isContainerType(type);

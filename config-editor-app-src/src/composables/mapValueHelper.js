export const insertAfterKeyInMap = (object, key, newKey, newValue) => {
  if (typeof object[newKey] !== 'undefined') {
    throw new Error('Cannot insert value. Key "' + newKey + '" already exists in object.');
  }
  const allKeys = Object.keys(object);
  let keyFound = false;
  allKeys.forEach((currentKey) => {
    if (currentKey === key) {
      keyFound = true;
      object[newKey] = newValue;
    } else if (keyFound) {
      const currentValue = object[currentKey];
      delete object[currentKey];
      object[currentKey] = currentValue;
    }
  });
};

export const renameInMap = (object, oldKey, newKey) => {
  const value = object[oldKey];
  insertAfterKeyInMap(object, oldKey, newKey, value);
  delete object[oldKey];
};

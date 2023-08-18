import { v4 as uuidv4 } from 'uuid';
import { AbstractListUtility } from './abstractListValueHelper';

export const MapUtility = AbstractListUtility();

const KEY_KEY = 'key';

const getItemKey = (item) => item[KEY_KEY];

const sortByKey = (list) => {
  const keys = Object.keys(list);
  keys.sort();
  const sorted = {};
  keys.forEach((key) => {
    sorted[key] = list[key];
  });
};

const flatten = (list) => {
  const result = [];
  Object.keys(list).forEach((id) => {
    result[list[id][KEY_KEY]] = list[id][MapUtility.KEY_VALUE];
  });
  return result;
};

const createItem = (value, key, weight, id) => {
  const item = {};
  item[MapUtility.KEY_UID] = typeof id !== 'undefined' ? id : uuidv4();
  item[MapUtility.KEY_WEIGHT] = typeof weight !== 'undefined' ? weight : 0;
  item[KEY_KEY] = key;
  item[MapUtility.KEY_VALUE] = value;
  return item;
};

const addValues = (list, values) => {
  const ids = [];
  Object.keys(values).forEach((key) => {
    const value = values[key];
    const item = createItem(value, key);
    list[item[MapUtility.KEY_UID]] = item;
    ids.push(item[MapUtility.KEY_UID]);
  });
  return ids;
};

const append = (list, key, value) => {
  const values = {};
  values[key] = value;
  return appendMultiple(list, values);
};

const appendMultiple = (list, values) => {
  const ids = addValues(list, values);
  return MapUtility.moveMultipleToEnd(list, ids);
};

const prepend = (list, key, value) => {
  const values = {};
  values[key] = value;
  return prependMultiple(list, values);
};

const prependMultiple = (list, values) => {
  const ids = addValues(list, values);
  return MapUtility.moveMultipleToFront(list, ids);
};

const insertAfter = (list, id, key, value) => {
  const values = {};
  values[key] = value;
  return insertMultipleAfter(list, id, values);
};

const insertMultipleAfter = (list, id, values) => {
  const ids = addValues(list, values);
  return MapUtility.moveMultipleAfter(list, ids, id);
};

const insertBefore = (list, id, key, value) => {
  const values = {};
  values[key] = value;
  return insertMultipleBefore(list, id, values);
};

const insertMultipleBefore = (list, id, values) => {
  const ids = addValues(list, values);
  return MapUtility.moveMultipleBefore(list, ids, id);
};

const equals = (list1, list2, strict) => {
  if (!strict) {
    list1 = flatten(sortByKey(list1));
    list2 = flatten(sortByKey(list2));
  }
  return JSON.stringify(list1) === JSON.stringify(list2);
};

MapUtility.KEY_KEY = KEY_KEY;
MapUtility.flatten = flatten;
MapUtility.appendMultiple = appendMultiple;
MapUtility.append = append;
MapUtility.prependMultiple = prependMultiple;
MapUtility.prepend = prepend;
MapUtility.insertMultipleAfter = insertMultipleAfter;
MapUtility.insertAfter = insertAfter;
MapUtility.insertMultipleBefore = insertMultipleBefore;
MapUtility.insertBefore = insertBefore;
MapUtility.sortByKey = sortByKey;
MapUtility.equals = equals;
MapUtility.getItemKey = getItemKey;

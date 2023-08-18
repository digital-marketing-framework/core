import { v4 as uuidv4 } from 'uuid';
import { AbstractListUtility } from './abstractListValueHelper';

export const ListUtility = AbstractListUtility();

const flatten = (list) => {
  const result = [];
  Object.keys(list).forEach((id) => {
    result.push(list[id][ListUtility.KEY_VALUE]);
  });
  return result;
};

const createItem = (value, weight, id) => {
  const item = {};
  item[ListUtility.KEY_UID] = typeof id !== 'undefined' ? id : uuidv4();
  item[ListUtility.KEY_WEIGHT] = typeof weight !== 'undefined' ? weight : 0;
  item[ListUtility.KEY_VALUE] = value;
  return item;
};

const addValues = (list, values) => {
  const ids = [];
  values.forEach((value) => {
    const item = createItem(value);
    list[item[ListUtility.KEY_UID]] = item;
    ids.push(item[ListUtility.KEY_UID]);
  });
  return ids;
};

const append = (list, value) => {
  return appendMultiple(list, [value]);
};

const appendMultiple = (list, values) => {
  const ids = addValues(list, values);
  return ListUtility.moveMultipleToEnd(list, ids);
};

const prepend = (list, value) => {
  return prependMultiple(list, [value]);
};

const prependMultiple = (list, values) => {
  const ids = addValues(list, values);
  return ListUtility.moveMultipleToFront(list, ids);
};

const insertAfter = (list, id, value) => {
  return insertMultipleAfter(list, id, [value]);
};

const insertMultipleAfter = (list, id, values) => {
  const ids = addValues(list, values);
  return ListUtility.moveMultipleAfter(list, ids, id);
};

const insertBefore = (list, id, value) => {
  return insertMultipleBefore(list, id, [value]);
};

const insertMultipleBefore = (list, id, values) => {
  const ids = addValues(list, values);
  return ListUtility.moveMultipleBefore(list, ids, id);
};

const equals = (list1, list2, strict) => {
  if (!strict) {
    list1 = flatten(ListUtility.sort(list1));
    list2 = flatten(ListUtility.sort(list2));
  }
  return JSON.stringify(list1) === JSON.stringify(list2);
};

ListUtility.flatten = flatten;
ListUtility.appendMultiple = appendMultiple;
ListUtility.append = append;
ListUtility.prependMultiple = prependMultiple;
ListUtility.prepend = prepend;
ListUtility.insertMultipleAfter = insertMultipleAfter;
ListUtility.insertAfter = insertAfter;
ListUtility.insertMultipleBefore = insertMultipleBefore;
ListUtility.insertBefore = insertBefore;
ListUtility.equals = equals;

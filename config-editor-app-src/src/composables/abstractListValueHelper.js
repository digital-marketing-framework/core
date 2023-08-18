import { cloneValue, flip } from './valueHelper';

const KEY_UID = 'uuid';
const KEY_WEIGHT = 'weight';
const KEY_VALUE = 'value';

const WEIGHT_DELTA = 100;
const WEIGHT_START = 10000;

const sort = (list) => {
  list = cloneValue(list);
  const ids = Object.keys(list);
  ids.sort((id1, id2) => list[id1][KEY_WEIGHT] - list[id2][KEY_WEIGHT]);
  const sorted = {};
  ids.forEach((id) => {
    sorted[id] = list[id];
  });
  return sorted;
};

const getItemId = (item) => item[KEY_UID];

const getItemValue = (item) => item[KEY_VALUE];

const getItemWeight = (item) => item[KEY_WEIGHT];

const removeMultiple = (list, idsToRemove) => {
  idsToRemove.forEach((id) => {
    delete list[id];
  });
  return list;
};

const remove = (list, id) => {
  return removeMultiple(list, [id]);
};

const findLast = (list) => {
  list = sort(list);
  const ids = Object.keys(list);
  if (ids.length === 0) {
    return null;
  }
  return list[ids[ids.length - 1]];
};

const findFirst = (list) => {
  list = sort(list);
  const ids = Object.keys(list);
  if (ids.length === 0) {
    return null;
  }
  return list[ids[0]];
};

const findPredecessor = (list, id, amount) => {
  if (typeof amount === 'undefined') {
    amount = 1;
  }
  list = sort(list);
  const ids = Object.keys(list);
  const positions = flip(ids);
  const position = positions[id];
  if (position < amount) {
    return null;
  }
  return list[ids[position - amount]];
};

const findAllPredecessors = (list, id, includeInitialItem) => {
  list = sort(list);
  const result = {};
  const ids = Object.keys(list);
  for (let i = 0; i < ids.length; i++) {
    const itemId = ids[i];
    if (itemId === id && !includeInitialItem) {
      break;
    }
    result[itemId] = list[itemId];
    if (itemId === id && includeInitialItem) {
      break;
    }
  }
  return result;
};

const isFirst = (list, id) => {
  return findPredecessor(list, id) === null;
};

const findSuccessor = (list, id, amount) => {
  if (typeof amount === 'undefined') {
    amount = 1;
  }
  list = sort(list);
  const ids = Object.keys(list);
  const positions = flip(ids);
  const position = parseInt(positions[id]);
  if (position + amount >= ids.length) {
    return null;
  }
  return list[ids[position + amount]];
};

const findAllSuccessors = (list, id, includeInitialItem) => {
  list = sort(list);
  const result = {};
  let idFound = false;
  const ids = Object.keys(list);
  for (let i = 0; i < ids.length; i++) {
    const itemId = ids[i];
    if (itemId === id) {
      idFound = true;
      if (!includeInitialItem) {
        continue;
      }
      if (idFound) {
        result[itemId] = list[itemId];
      }
    }
  }
  return result;
};

const isLast = (list, id) => {
  return findSuccessor(list, id) === null;
};

const moveMultipleToEnd = (list, ids) => {
  const lastItem = findLast(removeMultiple(cloneValue(list), ids));
  let weight = lastItem !== null ? lastItem[KEY_WEIGHT] + WEIGHT_DELTA : WEIGHT_START;
  ids.forEach((id) => {
    list[id][KEY_WEIGHT] = weight;
    weight += WEIGHT_DELTA;
  });
  return list;
};

const moveToEnd = (list, id) => {
  return moveMultipleToEnd(list[id]);
};

const moveMultipleBefore = (list, ids, beforeId) => {
  const previousItem = findPredecessor(removeMultiple(cloneValue(list), ids), beforeId);
  if (previousItem === null) {
    return moveMultipleToFront(list, ids);
  }
  return moveMultipleBetween(list, previousItem[KEY_UID], beforeId, ids);
};

const moveBefore = (list, id, beforeId) => {
  return moveMultipleBefore(list, [id], beforeId);
};

const moveMultipleToFront = (list, ids) => {
  const firstItem = findFirst(removeMultiple(cloneValue(list), ids));
  let weight = firstItem !== null ? firstItem[KEY_WEIGHT] - WEIGHT_DELTA : WEIGHT_START;
  ids.forEach((id) => {
    list[id][KEY_WEIGHT] = weight;
    weight -= WEIGHT_DELTA;
  });
  return list;
};

const moveToFront = (list, id) => {
  return moveMultipleToFront(list, [id]);
};

const moveMultipleAfter = (list, ids, afterId) => {
  const nextItem = findSuccessor(removeMultiple(cloneValue(list), ids), afterId);
  if (nextItem === null) {
    return moveMultipleToEnd(list, ids);
  }
  return moveMultipleBetween(list, afterId, nextItem[KEY_UID], ids);
};

const moveAfter = (list, id, afterId) => {
  return moveMultipleAfter(list, [id], afterId);
};

const moveMultipleBetween = (list, previousId, nextId, ids) => {
  const reducedList = removeMultiple(cloneValue(list), ids);
  const allPreviousIds = Object.keys(findAllPredecessors(reducedList, previousId, true));
  const allNextIds = Object.keys(findAllSuccessors(reducedList, nextId, true));
  const minItems = ids.length;
  const maxItems = Math.min(allPreviousIds.length, allNextIds.length) + ids.length;

  let winnerPreviousId = null;
  let winnerNextId = null;
  let winnerIds = null;
  let winnerCount = null;
  let winnerRange = null;

  const previousIds = cloneValue(allPreviousIds);
  const currentPreviousIds = [];
  while (previousIds.length > 0) {
    const currentPreviousId = previousIds.pop();

    const nextIds = cloneValue(allNextIds);
    const currentNextIds = [];
    while (nextIds.length > 0) {
      const currentNextId = nextIds.shift();
      const currentIds = [...currentPreviousIds, ...ids, ...currentNextIds];
      const currentCount = currentIds.length;
      const currentRange = list[currentNextId][KEY_WEIGHT] - list[currentPreviousId][KEY_WEIGHT];

      if (
        // if it is easier to rearrange all items in one direction, don't bother
        currentCount <= maxItems &&
        // if there is already a smaller fitting subset, don't bother
        (winnerIds === null ||
          currentCount < winnerCount ||
          (currentCount === winnerCount && currentRange > winnerRange)) &&
        // if this subset does not fit, don't bother
        currentRange > currentCount
      ) {
        winnerIds = currentIds;
        winnerPreviousId = currentPreviousId;
        winnerNextId = currentNextId;
        winnerCount = currentCount;
        winnerRange = currentRange;
        break;
      }
      currentNextIds.push(currentNextId);
    }
    if (winnerIds !== null && winnerCount === minItems) {
      break;
    }
    currentPreviousIds.push(currentPreviousId);
  }

  if (winnerIds === null) {
    if (allPreviousIds.length < allNextIds.length) {
      return moveMultipleToFront(list, [...allPreviousIds, ...ids]);
    } else {
      return moveMultipleToEnd(list, [...ids, ...allNextIds]);
    }
  }

  const previousWeight = list[winnerPreviousId][KEY_WEIGHT];
  const nextWeight = list[winnerNextId][KEY_WEIGHT];
  const delta = Math.ceil((nextWeight - previousWeight) / (winnerIds.length + 1));
  let weight = previousWeight + delta;
  winnerIds.forEach((id) => {
    list[id][KEY_WEIGHT] = weight;
    weight += delta;
  });
  return list;
};

const moveBetween = (list, previousId, nextId, id) => {
  return moveMultipleBetween(list, previousId, nextId, [id]);
};

export const AbstractListUtility = () => {
  return {
    KEY_UID: KEY_UID,
    KEY_WEIGHT: KEY_WEIGHT,
    KEY_VALUE: KEY_VALUE,

    sort: sort,
    removeMultiple: removeMultiple,
    remove: remove,
    findLast: findLast,
    findFirst: findFirst,
    findPredecessor: findPredecessor,
    findAllPredecessors: findAllPredecessors,
    isFirst: isFirst,
    findSuccessor: findSuccessor,
    findAllSuccessors: findAllSuccessors,
    isLast: isLast,
    moveMultipleToEnd: moveMultipleToEnd,
    moveToEnd: moveToEnd,
    moveMultipleBefore: moveMultipleBefore,
    moveBefore: moveBefore,
    moveMultipleToFront: moveMultipleToFront,
    moveToFront: moveToFront,
    moveMultipleAfter: moveMultipleAfter,
    moveAfter: moveAfter,
    moveMultipleBetween: moveMultipleBetween,
    moveBetween: moveBetween,
    getItemId: getItemId,
    getItemValue: getItemValue,
    getItemWeight: getItemWeight
  };
};

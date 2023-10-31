import { useDmfStore } from '../stores/dmf';

export const WARNING_INCLUDES_CHANGED = 'includesChanged';
export const WARNING_DOCUMENT_INVALID = 'documentInvalid';

const WARNINGS = {};
WARNINGS[WARNING_INCLUDES_CHANGED] = 'Includes have changed';
WARNINGS[WARNING_DOCUMENT_INVALID] = 'Document validation failed';

// actions

const writeMessage = (store, message, type) => {
  store.messages.push({ text: message, type: type || 'info' });
  // store.triggerRerender();
};

const removeMessage = (store, index) => {
  store.messages.splice(index, 1);
  // store.triggerRerender();
};

const setWarning = (store, key, action, actionLabel) => {
  const warning = {
    message: WARNINGS[key] || key
  };
  if (action) {
    warning.action = action;
  }
  if (actionLabel) {
    warning.actionLabel = actionLabel;
  }
  store.warnings[key] = warning;
};

const unsetWarning = (store, key) => {
  if (typeof store.warnings[key] !== 'undefined') {
    delete store.warnings[key];
  }
};

export const useNotifications = (store) => {
  store = store || useDmfStore();
  return {
    writeMessage: (message, type) => writeMessage(store, message, type),
    removeMessage: (index) => removeMessage(store, index),
    setWarning: (key, action, actionLabel) => setWarning(store, key, action, actionLabel),
    unsetWarning: (key) => unsetWarning(store, key)
  };
};

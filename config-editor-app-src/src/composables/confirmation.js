import { useDmfStore } from '../stores/dmf';

const requestConfirmation = (store, callback, headline, text, yes, no) => {
  if (store.confirmDialog.open) {
    throw new Error('dialog already open with another question');
  }
  store.confirmDialog.headline = headline;
  store.confirmDialog.text = text;
  if (yes) {
    store.confirmDialog.yes = yes;
  }
  if (no) {
    store.confirmDialog.no = no;
  }
  store.confirmDialog.callback = callback;
  store.confirmDialog.open = true;
};

export const useConfirmation = (store) => {
  store = store || useDmfStore();
  return {
    requestConfirmation: (callback, headline, text, yes, no) =>
      requestConfirmation(store, callback, headline, text, yes, no)
  };
};

export const debounce = (func, wait, immediate) => {
  let timeout;
  return function () {
    var context = this,
      args = arguments;
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(function () {
      timeout = null;
      if (!immediate) {
        func.apply(context, args);
      }
    }, wait);
    if (callNow) func.apply(context, args);
  };
};

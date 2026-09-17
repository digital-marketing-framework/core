/* eslint-disable no-undef */
const prefixSelector = require('postcss-prefix-selector');

module.exports = {
  plugins: [
    require('tailwindcss'),
    prefixSelector({
      prefix: '.dmf-configuration-document-editor-stage',

      // Optional transform callback for case-by-case overrides
      transform: function (prefix, selector, prefixedSelector) {
          // mainly for applying font
          if (selector === 'html' || selector === 'body') {
              return prefix;
          }

          // root identifier
          if (selector === prefix) {
              return selector;
          }

          // Selectors that already start at the root scope themselves, such as
          // the colour scheme attribute on the stage. Prefixing them again
          // would ask for a stage inside a stage, which never matches.
          if (selector.startsWith(prefix + '[') || selector.startsWith(prefix + ':')) {
              return selector;
          }

          return prefix + ' ' + selector;
      }
  }),
  require('autoprefixer')
]};

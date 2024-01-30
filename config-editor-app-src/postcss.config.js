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

          return prefix + ' ' + selector;
      }
  }),
  require('autoprefixer')
]};

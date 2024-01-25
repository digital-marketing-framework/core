/* eslint-disable no-undef */
const prefixSelector = require('postcss-prefix-selector');

module.exports = {
  plugins: [
    require('tailwindcss'),
    prefixSelector({
      prefix: '.dmf-configuration-document-editor-stage',
      exclude: [
          /\[/, // starts with [ (e.g. [role])
      ],

      // Optional transform callback for case-by-case overrides
      transform: function (prefix, selector, prefixedSelector) {
          // mainly for applying font
          if (selector === 'html') {
              return prefix + " *";
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

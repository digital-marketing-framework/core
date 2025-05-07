/* eslint-disable no-undef */
/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'tw-',
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        anyrel: ['ui-sans-serif, system-ui']
      }
    }
  },
  plugins: [
    require('@headlessui/tailwindcss'),
    require("@tailwindcss/forms")({
      strategy: 'class'
    })
  ]
};

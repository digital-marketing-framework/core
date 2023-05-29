/* eslint-disable no-undef */
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        caveat: ['Caveat']
      }
    }
  },
  plugins: [require('@headlessui/tailwindcss'), require('@tailwindcss/forms')]
};

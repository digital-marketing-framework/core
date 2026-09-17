/* eslint-disable no-undef */
/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'tw-',
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        anyrel: ['ui-sans-serif, system-ui']
      },

      /* Semantic colours, each backed by a token from src/assets/tokens.css,
       * where the light and dark value of every one is declared. Components
       * use these and not Tailwind's own palette, so a colour is decided in
       * one place rather than in 19 files.
       *
       * The class name drops what the utility already says: the token
       * --dmf-field-bg is `field` here, because it is only ever a background
       * (tw-bg-field). `ink` reads as the text colour on a surface,
       * `line` as its border.
       */
      colors: {
        surface: 'var(--dmf-surface)',
        'surface-bar': 'var(--dmf-surface-bar)',
        'surface-readonly': 'var(--dmf-surface-readonly)',
        'surface-hover': 'var(--dmf-surface-hover)',
        veil: 'var(--dmf-surface-veil)',
        line: 'var(--dmf-border)',
        ink: 'var(--dmf-text)',
        muted: 'var(--dmf-text-muted)',
        'muted-hover': 'var(--dmf-text-muted-hover)',

        field: 'var(--dmf-field-bg)',
        'field-ink': 'var(--dmf-field-text)',
        'field-line': 'var(--dmf-field-border)',
        'field-selected': 'var(--dmf-field-selected-bg)',
        'field-accent': 'var(--dmf-field-accent-hover)',
        input: 'var(--dmf-input-ring)',
        focus: 'var(--dmf-focus-ring)',
        subtle: 'var(--dmf-ring-subtle)',
        placeholder: 'var(--dmf-placeholder)',
        chip: 'var(--dmf-chip-bg)',
        'chip-line': 'var(--dmf-chip-border)',

        accent: 'var(--dmf-container-accent)',
        'accent-hover': 'var(--dmf-container-accent-hover)',
        'accent-line': 'var(--dmf-container-border-subtle)',
        'accent-selected': 'var(--dmf-container-selected-bg)',
        'accent-selected-ink': 'var(--dmf-container-selected-text)',
        code: 'var(--dmf-code-bg)',
        backdrop: 'var(--dmf-backdrop)',
        notice: 'var(--dmf-notice-bg)',

        button: 'var(--dmf-button-bg)',
        'button-hover': 'var(--dmf-button-bg-hover)',
        'button-ink': 'var(--dmf-button-text)',
        danger: 'var(--dmf-danger-text)',
        'danger-bg': 'var(--dmf-danger-bg)',
        'danger-strong': 'var(--dmf-danger-border)',
        'danger-solid': 'var(--dmf-danger-solid)',
        'danger-hover': 'var(--dmf-danger-bg-hover)',

        rail: 'var(--dmf-rail-default)',
        'rail-hover': 'var(--dmf-rail-default-hover)',
        'rail-scalar': 'var(--dmf-rail-scalar)',
        'rail-scalar-hover': 'var(--dmf-rail-scalar-hover)',
        'rail-dynamic': 'var(--dmf-rail-dynamic)',
        'rail-dynamic-hover': 'var(--dmf-rail-dynamic-hover)'
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

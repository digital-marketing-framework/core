# Configuration editor

The Vue application behind Anyrel's configuration editor. It is embedded into a
CMS backend — or any other PHP application — as a single element, the *stage*:

```html
<div class="dmf-configuration-document-editor-stage" data-dmf-color-scheme="auto">
```

Everything the editor needs arrives as `data-*` attributes on the textarea it
replaces (see `src/utils/environmentLinker.js`), and everything it renders stays
inside that stage. `postcss-prefix-selector` scopes every generated rule under
the stage class, and all utility classes carry the `tw-` prefix, so the editor's
styles cannot leak into the surrounding application.

## Working on colours

**Colours are declared in `src/assets/tokens.css` and nowhere else.** Components
use semantic classes mapped to those tokens in `tailwind.config.js` —
`tw-bg-surface`, `tw-text-muted`, `tw-border-line` — never Tailwind's own
palette. A `tw-bg-blue-100` in a component would look right in light mode and
wrong in dark, and nothing would fail to tell you.

Each token is declared once, as a light and a dark value:

```css
--dmf-field-bg: light-dark(#dbeafe, #1e3e74);
```

Hover states are derived from their resting colour with `color-mix()` rather
than picked, so changing a colour carries to its hover automatically.

Every token carries its light and dark value in `tokens.css`, and the contrast
targets they were checked against are noted there too.

## Colour scheme

Which half of each token applies is decided by the `color-scheme` property,
which `light-dark()` reads. The stage takes its value from the global setting
`backend.colorScheme`, delivered as `data-dmf-color-scheme`:

| Value | Behaviour |
|---|---|
| `auto` (default) | `color-scheme: inherit` — follow the application the editor is embedded in |
| `light` / `dark` | `color-scheme: only light` / `only dark` — override it |

`auto` is deliberately `inherit` and not `light dark`: `inherit` follows what the
surrounding document declares, while `light dark` would ask the browser and
ignore the host. An application that declares nothing counts as light, which is
the right answer for a light-only backend.

Nothing here knows about any particular CMS.

## Setup

```sh
npm install
npm run build
```

`npm run build` writes the bundle to `../res/assets/config-editor`, which is
committed, so a change to this app only reaches a CMS after a build. The Anyrel
development workspace wraps these in its own scripts; this package builds with
npm alone.

## Standalone development

```sh
npm run dev
```

`index.html` loads the app against static fixtures in `public/demo/` instead of a
CMS. The fixtures are committed snapshots and can be older than the current
schema, so fields may differ from what a real backend shows. Regenerating them
needs a running CMS, since they are captures of what the AJAX controllers
return.

The page declares no colour scheme of its own, so `auto` renders light there. To
see the dark scheme, set `data-dmf-color-scheme="dark"` on the stage, or run the
editor in a CMS.

## Linting

```sh
npm run lint
npm run format
```

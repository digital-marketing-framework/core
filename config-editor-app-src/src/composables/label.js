import { getAbsolutePath, getLeafKey } from '@/helpers/path';
import { useDmfStore } from '@/stores/dmf';
import { useValueSets } from './valueSets';

/**
 * Label expressions
 *
 * A label may contain placeholders. Each placeholder holds one or more terms separated
 * by "|"; the first term that resolves to a non-empty string wins.
 *
 *   {a}            value at path a
 *   {a|b}          value at path a, falling back to the value at path b
 *   {pretty(a)}    human-readable form of the value at path a
 *   {a|'Unnamed'}  value at path a, falling back to a literal
 *
 * Placeholders may be nested: the innermost one is resolved first, so {config/{type}/x}
 * works. A literal cannot contain "{", "}" or "|".
 *
 * A bare path resolves to the value's allowed/suggested-value label when the schema
 * defines one, otherwise to the raw value. pretty() differs only in what it does when
 * there is no such label: it derives one from the raw value. So pretty() means "the
 * human-readable form of this", not "always run the prettifier".
 */

/**
 * Splits an identifier into words: an acronym before a capitalised word, an optionally
 * capitalised lowercase run, a remaining uppercase run, or a digit run. Anything else
 * (separators, punctuation) is a boundary and is dropped.
 */
const LABEL_WORD_PATTERN = /\p{Lu}+(?=\p{Lu}\p{Ll})|\p{Lu}?\p{Ll}+|\p{Lu}+|\d+/gu;

/**
 * Derives a human-readable label from a machine-readable identifier.
 *
 *   fooBar          => Foo Bar        fooBAR   => Foo BAR
 *   first_name      => First Name     user2Id  => User 2 Id
 *   XMLHttpRequest  => XML Http Request         Straße => Straße
 *
 * A word is capitalised only when it is entirely lowercase, so acronyms survive intact.
 * An opaque identifier has no word structure to find and comes out no more readable than
 * it went in; a configured label is the answer for those.
 *
 * Must stay behaviourally identical to GeneralUtility::getLabelFromValue() in PHP. Both
 * are covered by packages/core/tests/Fixtures/pretty-label.json — change the fixture first.
 */
const prettifyLabel = (label) => {
  const words = String(label).match(LABEL_WORD_PATTERN) ?? [];
  return words
    .map((word) => (word === word.toLowerCase() ? word.charAt(0).toUpperCase() + word.slice(1) : word))
    .join(' ');
};

const getValueLabel = (store, value, path, currentPath) => {
  const { getAllowedValues, getSuggestedValues } = useValueSets(store);
  const allowedValues = getAllowedValues(path, currentPath);
  if (typeof allowedValues[value] !== 'undefined') {
    return allowedValues[value];
  }

  const suggestedValues = getSuggestedValues(path, currentPath);
  if (typeof suggestedValues[value] !== 'undefined') {
    return suggestedValues[value];
  }

  return value;
};

// term functions, keyed by the name used in an expression
const termFunctions = {
  pretty: (resolved) => (resolved.labelled ? resolved.value : prettifyLabel(resolved.value))
};

/**
 * Resolves a path to its value plus whether a value set supplied a label for it.
 * Container values have no scalar representation, so their leaf key stands in.
 */
const resolvePath = (store, referencePath, absolutePath) => {
  const value = store.getValue(referencePath, absolutePath);
  if (typeof value === 'undefined' || value === null) {
    return { value: '', labelled: false };
  }
  if (typeof value === 'object') {
    return { value: getLeafKey(referencePath, absolutePath), labelled: false };
  }
  const valueLabel = getValueLabel(store, value, referencePath, absolutePath);
  return { value: String(valueLabel), labelled: valueLabel !== value };
};

/** Splits on "|" while keeping quoted literals intact. */
const splitTerms = (body) => {
  const terms = [];
  let current = '';
  let inLiteral = false;
  for (const char of body) {
    if (char === "'") {
      inLiteral = !inLiteral;
      current += char;
    } else if (char === '|' && !inLiteral) {
      terms.push(current);
      current = '';
    } else {
      current += char;
    }
  }
  terms.push(current);
  return terms.map((term) => term.trim()).filter((term) => term !== '');
};

const resolveTerm = (store, term, absolutePath) => {
  const literal = term.match(/^'(.*)'$/);
  if (literal !== null) {
    return literal[1];
  }

  const call = term.match(/^([a-zA-Z]+)\((.+)\)$/);
  if (call !== null) {
    const [, functionName, argument] = call;
    const termFunction = termFunctions[functionName];
    if (typeof termFunction === 'undefined') {
      console.error('unknown label function "' + functionName + '"');
      return '';
    }
    return termFunction(resolvePath(store, argument, absolutePath));
  }

  return resolvePath(store, term, absolutePath).value;
};

/**
 * Replaces every placeholder in the expression, innermost first.
 *
 * @param resolvePlaceholder receives the placeholder body and returns its replacement
 */
const replacePlaceholders = (expression, resolvePlaceholder) => {
  let placeholderFound = true;
  while (placeholderFound) {
    placeholderFound = false;
    expression = expression.replace(/\{[^{}]+\}/, (match) => {
      placeholderFound = true;
      return resolvePlaceholder(match.substring(1, match.length - 1));
    });
  }
  return expression;
};

const processLabel = (store, label, path, currentPath) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  return replacePlaceholders(label, (body) => {
    for (const term of splitTerms(body)) {
      const resolved = resolveTerm(store, term, absolutePath);
      if (resolved !== '') {
        return resolved;
      }
    }
    return '';
  });
};

/**
 * Substitutes raw configuration values into a string that is used as an identifier
 * rather than shown to the user, such as a dynamic value set name. Never applies
 * value-set labels or prettifying, both of which would change the identifier.
 */
const interpolatePath = (store, template, path, currentPath) => {
  const absolutePath = getAbsolutePath(path, currentPath);
  return replacePlaceholders(template, (body) => {
    const value = store.getValue(body, absolutePath);
    if (typeof value === 'undefined' || value === null) {
      return '';
    }
    // A container has no scalar form; its leaf key identifies it.
    // "outboundRoutes/{../../../..}/all" relies on this to name the integration.
    if (typeof value === 'object') {
      return getLeafKey(body, absolutePath);
    }
    return String(value);
  });
};

const getLabel = (store, path, currentPath, schema) => {
  schema = schema || store.getSchema(path, currentPath, true);

  if (schema.hideLabel) {
    return '';
  }

  if (schema.label) {
    return processLabel(store, schema.label, path, currentPath);
  }

  // No label defined: the property name is the best available description.
  return prettifyLabel(getLeafKey(path, currentPath));
};

export const useLabelProcessor = (store) => {
  store = store || useDmfStore();
  return {
    getLabel: (path, currentPath) => getLabel(store, path, currentPath),
    processLabel: (label, path, currentPath) => processLabel(store, label, path, currentPath),
    interpolatePath: (template, path, currentPath) =>
      interpolatePath(store, template, path, currentPath),
    prettifyLabel: (label) => prettifyLabel(label),
    getValueLabel: (value, path, currentPath) => getValueLabel(store, value, path, currentPath)
  };
};

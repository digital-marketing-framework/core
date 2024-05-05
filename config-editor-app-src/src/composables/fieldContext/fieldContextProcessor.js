export const RESULT_YES = true;
export const RESULT_NO = false;
export const RESULT_MAYBE = 'maybe';

export const TYPE_UNKNOWN = 'UNKNOWN';

export const DEDICATED_FIELD_COLLECTOR = 'COLLECTOR_FIELD';

const isRequired = (context, name) => {
  const fields = context[name] || [];
  for (let i = 0; i < fields.length; i++) {
    if (fields[i].required) {
      return true;
    }
  }
  return false;
};

const getTypes = (context, name) => {
  const types = [];
  const fields = context[name] || [];
  fields.forEach((field) => {
    if (types.indexOf(field.type) === -1) {
      types.push(field.type);
    }
  });
  return types;
};

const is = (context, name, where, what) => {
  const fields = context[name] || [];
  let found = false;
  let foundOther = false;
  fields.forEach((field) => {
    if (field[where] === what) {
      found = true;
    } else {
      foundOther = true;
    }
  });
  if (found && !foundOther) {
    return RESULT_YES;
  }
  if (!found && foundOther) {
    return RESULT_NO;
  }
  return RESULT_MAYBE;
};

const isOfType = (context, name, type) => {
  if (is(context, name, 'type', TYPE_UNKNOWN) !== RESULT_NO) {
    return RESULT_MAYBE;
  }
  return is(context, name, 'type', type);
};

const isMultiValue = (context, name) => {
  return is(context, name, 'multiValue', true);
};

const getLabel = (context, name) => {
  const fields = context[name] || [];
  for (let i = 0; i < fields.length; i++) {
    if (fields[i].label) {
      return fields[i].label;
    }
  }
  return name;
};

const getDedication = (context, name) => {
  const fields = context[name] || [];
  for (let i = 0; i < fields.length; i++) {
    if (typeof fields[i].dedicated !== 'undefined') {
      return fields[i].dedicated;
    }
  }
  return null;
};

const getValues = (context, name) => {
  const values = [];
  const add = (value) => {
    if (values.indexOf(value) === -1) {
      values.push(value);
    }
  };

  const fields = context[name] || [];
  fields.forEach((field) => {
    const fieldValues = field.values || [];
    fieldValues.forEach((value) => {
      add(value);
    });
  });

  if (isOfType(context, name, 'BOOLEAN')) {
    add(true);
    add(false);
  }

  return values;
};

export const useFieldContextProcessor = () => {
  return {
    isRequired: (context, name) => isRequired(context, name),
    getTypes: (context, name) => getTypes(context, name),
    isOfType: (context, name, type) => isOfType(context, name, type),
    isMultiValue: (context, name) => isMultiValue(context, name),
    getLabel: (context, name) => getLabel(context, name),
    getValues: (context, name) => getValues(context, name),
    getDedication: (context, name) => getDedication(context, name)
  };
};

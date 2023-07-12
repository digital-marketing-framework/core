<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class ScalarValues
{
    public const REFERENCE_TYPE_KEY = 'key';
    public const REFERENCE_TYPE_VALUE = 'value';

    protected array $values;

    protected ValueSet $list;

    /**
     * @param array<string|int|bool,string> $list
     * @param array<string> $sets
     * @param array<string> $references
     */
    public function __construct() {
        $this->reset();
    }

    public function addValue(string|int|bool $value, ?string $label = null): void
    {
        if (!isset($this->values['list'])) {
            $this->values['list'] = new ValueSet();
        }
        $this->values['list']->addValue($value, $label);
    }

    public function addValueSet(string $name): void
    {
        $this->values['sets'][] = $name;
    }

    public function addReference(string $path, string $type = self::REFERENCE_TYPE_KEY): void
    {
        $this->values['references'][] = [
            'type' => $type,
            'path' => $path,
        ];
    }

    public function reset(): void
    {
        $this->values = [];
    }

    public function addCustomValue(string $valueType, mixed $value): void
    {
        $this->values[$valueType][] = $value;
    }

    public function toArray(): ?array
    {
        if (!empty($this->values)) {
            $values = $this->values;
            if (isset($values['list'])) {
                $values['list'] = $values['list']->toArray();
            }
            return $values;
        }
        return null;
    }

    public function getFirstValue(SchemaDocument $schemaDocument): string|int|bool|null
    {
        $values = $this->getValues($schemaDocument);
        if (empty($values)) {
            return null;
        }
        return reset(array_keys($values));
    }

    /**
     * @return array<string|int|bool,string>
     */
    public function getValues(SchemaDocument $schemaDocument): array
    {
        $values = new ValueSet();
        foreach ($this->values as $type => $valueConfig) {
            switch ($type) {
                case 'list':
                    $values->merge($valueConfig);
                    break;
                case 'sets':
                    foreach ($valueConfig as $setName) {
                        $set = $schemaDocument->getValueSet($setName) ?? new ValueSet();
                        $values->merge($set);
                    }
                    break;
                case 'references':
                    // NOTE references can't be calculated without a data object to be referenced, so we do not take those into account
                    // TODO do we need to do something about this? probably, because server-side evaluation is supposed to use the schema
                    //      however, if we evaluate a data object, then we have it to follow the references
                    //      maybe add a second parameter for the data object, optional
                    break;
                default:
                    // TODO trigger an event so that other packages have a chance to implement their way of fetching/generating values (e.g. route references for the distributor)
                    break;
            }
        }
        return $values->toArray();
    }
}

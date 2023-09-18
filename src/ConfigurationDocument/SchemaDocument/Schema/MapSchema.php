<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\MapUtility;

class MapSchema extends ListSchema
{
    public function __construct(
        SchemaInterface $valueSchema = new ContainerSchema(),
        protected StringSchema $nameSchema = new StringSchema('mapKey'),
        mixed $defaultValue = null,
    ) {
        parent::__construct($valueSchema, $defaultValue);
        $this->itemSchema->addProperty(MapUtility::KEY_KEY, $nameSchema);
        if ($this->valueSchema->getRenderingDefinition()->getLabel() === null) {
            $this->valueSchema->getRenderingDefinition()->setLabel(sprintf('{../%s}', MapUtility::KEY_KEY));
        }
    }

    public function getType(): string
    {
        return 'MAP';
    }

    public function getNameSchema(): StringSchema
    {
        return $this->nameSchema;
    }

    public function setNameSchema(StringSchema $nameSchema): void
    {
        $this->nameSchema = $nameSchema;
        $this->nameSchema->getRenderingDefinition()->setSkipHeader(true);
        $this->itemSchema->removeProperty(MapUtility::KEY_KEY);
        $this->itemSchema->addProperty(MapUtility::KEY_KEY, $nameSchema);
    }

    public function getValueSets(): array
    {
        return $this->mergeValueSets(parent::getValueSets(), $this->nameSchema->getValueSets());
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        $default = $this->defaultValue ?? [];
        $map = [];
        if (!empty($default)) {
            foreach ($default as $key => $value) {
                $map = MapUtility::append($map, $key, $value);
            }
        }

        return $map;
    }
}

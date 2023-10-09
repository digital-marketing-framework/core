<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\ListUtility;

class ListSchema extends Schema
{
    protected ContainerSchema $itemSchema;

    protected bool $dynamicOrder = false;

    public function __construct(
        protected SchemaInterface $valueSchema = new ContainerSchema(),
        mixed $defaultValue = null,
    ) {
        parent::__construct($defaultValue);
        $this->itemSchema = new ContainerSchema();
        $this->itemSchema->addProperty(ListUtility::KEY_UID, new StringSchema());
        $this->itemSchema->addProperty(ListUtility::KEY_WEIGHT, new IntegerSchema(0));
        $this->itemSchema->addProperty(ListUtility::KEY_VALUE, $this->valueSchema);
        $this->itemSchema->getRenderingDefinition()->setSkipInNavigation(true);
        $this->itemSchema->getRenderingDefinition()->setSkipHeader(true);
    }

    public function getType(): string
    {
        return 'LIST';
    }

    public function getItemSchema(): ContainerSchema
    {
        return $this->itemSchema;
    }

    public function getValueSchema(): SchemaInterface
    {
        return $this->valueSchema;
    }

    public function setValueSchema(SchemaInterface $valueSchema): void
    {
        $this->valueSchema = $valueSchema;
        $this->valueSchema->getRenderingDefinition()->setSkipHeader(true);
        $this->itemSchema->removeProperty(ListUtility::KEY_VALUE);
        $this->itemSchema->addProperty(ListUtility::KEY_VALUE, $valueSchema);
    }

    public function getDynamicOrder(): bool
    {
        return $this->dynamicOrder;
    }

    public function setDynamicOrder(bool $dynamicOrder): void
    {
        $this->dynamicOrder = $dynamicOrder;
    }

    public function getValueSets(): array
    {
        return $this->mergeValueSets(parent::getValueSets(), $this->valueSchema->getValueSets());
    }

    protected function getConfig(): array
    {
        if (SchemaDocument::$flattenSchema) {
            return parent::getConfig() + [
                'dynamicOrder' => $this->dynamicOrder,
                'itemTemplate' => $this->getItemSchema()->toArray(),
            ];
        }

        return parent::getConfig() + [
            'dynamicOrder' => $this->dynamicOrder,
            'item' => $this->getItemSchema()->toArray(),
        ];
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        $default = $this->defaultValue ?? [];
        $list = [];
        if (!empty($default)) {
            foreach ($default as $value) {
                $list = ListUtility::append($list, $value);
            }
        }

        return $list;
    }

    public function preSaveDataTransform(mixed &$value, SchemaDocument $schemaDocument): void
    {
        if ($value === null) {
            return;
        }

        if (empty($value)) {
            $value = (object)[];
        } else {
            foreach (array_keys($value) as $key) {
                if (!isset($value[$key])) {
                    continue;
                }

                $this->itemSchema->preSaveDataTransform($value[$key], $schemaDocument);
            }
        }
    }
}

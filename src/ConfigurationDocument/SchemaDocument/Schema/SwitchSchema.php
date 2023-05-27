<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

abstract class SwitchSchema extends ContainerSchema
{
    protected StringSchema $typeSchema;
    protected ContainerSchema $configSchema;

    abstract protected function getSwitchName(): string;

    public function getType(): string
    {
        return "SWITCH";
    }

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);

        $this->typeSchema = new StringSchema();
        $this->typeSchema->getRenderingDefinition()->setFormat('select');
        $this->typeSchema->getAllowedValues()->addValueSet($this->getSwitchName() . '/all');

        $this->configSchema = new ContainerSchema();
        $this->addProperty('type', $this->typeSchema);
        $this->addProperty('config', $this->configSchema);
    }

    public function getTypeSchema(): StringSchema
    {
        return $this->typeSchema;
    }

    public function getConfigSchema(?string $type = null): ?SchemaInterface
    {
        if ($type === null) {
            return $this->configSchema;
        }
        return $this->configSchema->getProperty($type)?->getSchema();
    }

    public function addItem(string $type, SchemaInterface $schema): void
    {
        $this->valueSets[$this->getSwitchName() . '/all'][] = $type;
        $property = $this->configSchema->addProperty($type, $schema);
        $property->getRenderingDefinition()->setVisibilityConditionByString('../type', $type);
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        if ($this->defaultValue !== null) {
            return $this->defaultValue;
        }
        $type = $this->typeSchema->getDefaultValue($schemaDocument);
        $configProperty = $this->configSchema->getProperty($type);
        if ($configProperty === null) {
            throw new DigitalMarketingFrameworkException(sprintf('config type "%s" not found in switch schema', $type));
        }
        $config = $configProperty->getSchema()->getDefaultValue($schemaDocument);
        return [
            'type' => $type,
            'config' => [
                $type => $config,
            ],
        ];
    }
}

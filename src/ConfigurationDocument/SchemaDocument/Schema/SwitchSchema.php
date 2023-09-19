<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

class SwitchSchema extends ContainerSchema
{
    public const KEY_TYPE = 'type';

    public const KEY_CONFIG = 'config';

    protected StringSchema $typeSchema;

    protected ContainerSchema $configSchema;

    public function getType(): string
    {
        return 'SWITCH';
    }

    public function __construct(
        protected string $switchName,
        mixed $defaultValue = null,
    ) {
        parent::__construct($defaultValue);
        $this->getRenderingDefinition()->setLabel('{type}');

        $this->typeSchema = new StringSchema();
        $this->typeSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $this->typeSchema->getRenderingDefinition()->addTrigger(RenderingDefinitionInterface::TRIGGER_SWITCH);
        $this->typeSchema->getAllowedValues()->addValueSet($this->switchName . '/all');

        $this->configSchema = new ContainerSchema();
        $this->configSchema->getRenderingDefinition()->setSkipInNavigation(true);

        $this->addProperty(static::KEY_TYPE, $this->typeSchema);
        $this->addProperty(static::KEY_CONFIG, $this->configSchema);
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
        $this->addValueToValueSet($this->switchName . '/all', $type);
        $schema->getRenderingDefinition()->setSkipHeader(true);
        $schema->getRenderingDefinition()->setNavigationItem(false);
        $this->configSchema->addProperty($type, $schema);
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        if ($this->defaultValue !== null) {
            return $this->defaultValue;
        }

        $type = $this->typeSchema->getDefaultValue($schemaDocument);
        $configProperty = $this->configSchema->getProperty($type);
        if (!$configProperty instanceof ContainerProperty) {
            throw new DigitalMarketingFrameworkException(sprintf('config type "%s" not found in switch schema', $type));
        }

        $config = $configProperty->getSchema()->getDefaultValue($schemaDocument);

        return [
            static::KEY_TYPE => $type,
            static::KEY_CONFIG => [
                $type => $config,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $switchConfig
     */
    public static function getSwitchType(array $switchConfig): string
    {
        if (!isset($switchConfig[static::KEY_TYPE])) {
            throw new DigitalMarketingFrameworkException('no switch type found');
        }

        return $switchConfig[static::KEY_TYPE];
    }

    /**
     * @param array<string,mixed> $switchConfig
     */
    public static function getSwitchConfiguration(array $switchConfig): mixed
    {
        $type = static::getSwitchType($switchConfig);
        if (!isset($switchConfig[static::KEY_CONFIG][$type])) {
            throw new DigitalMarketingFrameworkException(sprintf('config type "%s" not found in switch configuration', $type));
        }

        return $switchConfig[static::KEY_CONFIG][$type];
    }

    public function preSaveDataTransform(mixed &$value, SchemaDocument $schemaDocument): void
    {
        if ($value === null) {
            return;
        }

        if (isset($value[static::KEY_TYPE])) {
            $this->typeSchema->preSaveDataTransform($value[static::KEY_TYPE], $schemaDocument);
        }

        foreach ($this->configSchema->getProperties() as $property) {
            if (isset($value[static::KEY_CONFIG][$property->getName()])) {
                $property->getSchema()->preSaveDataTransform($value[static::KEY_CONFIG][$property->getName()], $schemaDocument);
            }
        }
    }
}

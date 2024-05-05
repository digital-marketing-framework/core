<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;

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

        $this->addProperty(static::KEY_TYPE, $this->typeSchema)->setWeight(0);
        $this->addProperty(static::KEY_CONFIG, $this->configSchema);
    }

    public function getTypeSchema(): StringSchema
    {
        return $this->typeSchema;
    }

    public function getConfigSchema(): ContainerSchema
    {
        return $this->configSchema;
    }

    public function getTypeSpecificConfigSchema(string $type): ?SchemaInterface
    {
        return $this->configSchema->getProperty($type)?->getSchema();
    }

    public function addItem(string $type, SchemaInterface $schema, ?string $label = null): void
    {
        $this->addValueToValueSet($this->switchName . '/all', $type, $label);
        $schema->getRenderingDefinition()->setSkipHeader(true);
        $schema->getRenderingDefinition()->setNavigationItem(false);
        $this->configSchema->addProperty($type, $schema);
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
}

<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class InheritableIntegerSchema extends ContainerSchema
{
    public const KEY_INHERITED = 'inherited';

    public const KEY_CUSTOM_VALUE = 'customValue';

    public const VALUE_INHERITED_YES = 'yes';

    public const VALUE_INHERITED_NO = 'no';

    protected StringSchema $inheritedSchema;

    protected IntegerSchema $customValueSchema;

    public function __construct(?int $defaultValue = null)
    {
        parent::__construct($defaultValue);
        $this->getRenderingDefinition()->setNavigationItem(false);
        $this->getRenderingDefinition()->setSkipHeader(true);

        $inheritedDefaultValue = $defaultValue === null ? static::VALUE_INHERITED_YES : static::VALUE_INHERITED_NO;
        $customDefaultValue = $defaultValue ?? 0;

        $this->inheritedSchema = new StringSchema($inheritedDefaultValue);
        $this->inheritedSchema->getRenderingDefinition()->setLabel('Inherit cache lifetime');
        $this->inheritedSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $this->inheritedSchema->getAllowedValues()->addValue(static::VALUE_INHERITED_YES);
        $this->inheritedSchema->getAllowedValues()->addValue(static::VALUE_INHERITED_NO);
        $this->addProperty(static::KEY_INHERITED, $this->inheritedSchema);

        $this->customValueSchema = new IntegerSchema($customDefaultValue);
        $this->customValueSchema->getRenderingDefinition()->setLabel('Cache lifetime (seconds)');
        $this->customValueSchema->getRenderingDefinition()
            ->addVisibilityConditionByValue('../' . static::KEY_INHERITED)
            ->addValue(static::VALUE_INHERITED_NO)
        ;
        $this->addProperty(static::KEY_CUSTOM_VALUE, $this->customValueSchema);
    }

    public function addReference(string $path, ?string $label = null, ?string $icon = null): void
    {
        $this->inheritedSchema->getRenderingDefinition()->addReference($path, $label, $icon);
    }

    /**
     * @param array{inherited?:bool,customValue?:int} $config
     */
    public static function convert(array $config): ?int
    {
        $inherited = $config[static::KEY_INHERITED] ?? static::VALUE_INHERITED_YES;
        if ($inherited === static::VALUE_INHERITED_YES) {
            return null;
        }

        return $config[static::KEY_CUSTOM_VALUE] ?? 0;
    }
}

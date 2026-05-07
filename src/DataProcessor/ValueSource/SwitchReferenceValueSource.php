<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\MapUtility;

class SwitchReferenceValueSource extends ValueSource
{
    public const WEIGHT = 4;

    public const KEY_SWITCH = 'switch';

    public const KEY_MAP_NAME = 'reference';

    public const DEFAULT_MAP_NAME = '';

    public const KEY_INVERT = 'invert';

    public const DEFAULT_INVERT = false;

    public const KEY_USE_DEFAULT = 'useDefault';

    public const DEFAULT_USE_DEFAULT = false;

    public const KEY_DEFAULT = 'default';

    public const DEFAULT_DEFAULT = '';

    public function build(): ?string
    {
        $switchConfig = $this->getConfig(static::KEY_SWITCH);
        $switchValue = $this->dataProcessor->processValue($switchConfig, $this->context->copy());

        if ($switchValue === null) {
            return null;
        }

        $switchValue = (string)$switchValue;

        $map = $this->context->getConfiguration()->getValueMapConfiguration($this->getStringConfig(static::KEY_MAP_NAME));
        if ($map !== null) {
            $map = MapUtility::flatten($map);
            if ($this->getBoolConfig(static::KEY_INVERT)) {
                $map = array_flip($map);
            }

            if (isset($map[$switchValue])) {
                return (string)$map[$switchValue];
            }
        }

        if ($this->getBoolConfig(static::KEY_USE_DEFAULT)) {
            return $this->getStringConfig(static::KEY_DEFAULT);
        }

        return null;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();

        $switchSchema = new CustomSchema(ValueSchema::TYPE);
        $schema->addProperty(static::KEY_SWITCH, $switchSchema);

        $mapNameSchema = new StringSchema(static::DEFAULT_MAP_NAME);
        $mapNameSchema->setRequired();
        $mapNameSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $mapNameSchema->getAllowedValues()->addValue(static::DEFAULT_MAP_NAME, 'Please select');
        $mapNameSchema->getAllowedValues()->addReference('/dataProcessing/valueMaps/*', label: '{key}');
        $schema->addProperty(static::KEY_MAP_NAME, $mapNameSchema);

        $schema->addProperty(static::KEY_INVERT, new BooleanSchema(static::DEFAULT_INVERT));

        $useDefaultSchema = new BooleanSchema(static::DEFAULT_USE_DEFAULT);
        $schema->addProperty(static::KEY_USE_DEFAULT, $useDefaultSchema);

        $defaultSchema = new StringSchema(static::DEFAULT_DEFAULT);
        $defaultSchema->getRenderingDefinition()->addVisibilityConditionByValue('../' . static::KEY_USE_DEFAULT)->addValue(true);
        $schema->addProperty(static::KEY_DEFAULT, $defaultSchema);

        return $schema;
    }
}

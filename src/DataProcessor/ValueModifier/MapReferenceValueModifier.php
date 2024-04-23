<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\MapUtility;

class MapReferenceValueModifier extends ValueModifier
{
    public const WEIGHT = 40;

    public const KEY_MAP_NAME = 'reference';

    public const DEFAULT_MAP_NAME = '';

    public const KEY_INVERT = 'invert';

    public const DEFAULT_INVERT = false;

    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        $map = $this->context->getConfiguration()->getValueMapConfiguration($this->getConfig(static::KEY_MAP_NAME));
        if ($map !== null) {
            $map = MapUtility::flatten($map);
            if ($this->getConfig(static::KEY_INVERT)) {
                $map = array_flip($map);
            }

            return $map[(string)$value] ?? $value;
        }

        return $value;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $mapNameSchema = new StringSchema();
        $mapNameSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $mapNameSchema->getAllowedValues()->addReference('/valueMaps/*', label: '{key}');
        $schema->addProperty(static::KEY_MAP_NAME, $mapNameSchema);
        $schema->addProperty(static::KEY_INVERT, new BooleanSchema(false));

        return $schema;
    }
}

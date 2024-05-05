<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Utility\MapUtility;

/**
 * @extends DefaultValueSchemaProcessor<MapSchema>
 */
class MapDefaultValueSchemaProcessor extends DefaultValueSchemaProcessor
{
    /**
     * @return array<string,mixed>
     */
    public function getDefaultValue(SchemaInterface $schema): array
    {
        $default = parent::getDefaultValue($schema) ?? [];

        if (!is_array($default)) {
            throw new DigitalMarketingFrameworkException('Default value for map type must be an array');
        }

        $map = [];
        foreach ($default as $key => $value) {
            $map = MapUtility::append($map, $key, $value);
        }

        return $map;
    }
}

<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Utility\ListUtility;

/**
 * @extends DefaultValueSchemaProcessor<ListSchema>
 */
class ListDefaultValueSchemaProcessor extends DefaultValueSchemaProcessor
{
    /**
     * @return array<mixed>
     */
    public function getDefaultValue(SchemaInterface $schema): array
    {
        $default = parent::getDefaultValue($schema) ?? [];

        if (!is_array($default)) {
            throw new DigitalMarketingFrameworkException('Default value for list type must be an array');
        }

        $list = [];
        foreach ($default as $value) {
            $list = ListUtility::append($list, $value);
        }

        return $list;
    }
}

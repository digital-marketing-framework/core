<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class PrefixDataMapper extends DataMapper
{
    public const WEIGHT = 100;

    public const KEY_PREFIX = 'prefix';

    public const DEFAULT_PREFIX = '';

    public const KEY_ACTION = 'action';

    public const DEFAULT_ACTION = 'add';

    public const ACTION_ADD = 'add';

    public const ACTION_REMOVE = 'remove';

    protected function processFieldName(string $fieldName, string $action, string $prefix): string
    {
        switch ($action) {
            case static::ACTION_ADD:
                return $prefix . $fieldName;
            case static::ACTION_REMOVE:
                if (str_starts_with($fieldName, $prefix)) {
                    return substr($fieldName, strlen($prefix));
                }

                return $fieldName;
            default:
                throw new DigitalMarketingFrameworkException(sprintf('unknown prefix action "%s"', $action));
        }
    }

    public function mapData(DataInterface $target): DataInterface
    {
        $prefix = $this->getConfig(static::KEY_PREFIX);
        $action = $this->getConfig(static::KEY_ACTION);

        if ($prefix === '') {
            return $target;
        }

        $newData = [];
        // remove all values first to avoid name conflicts
        foreach ($target as $fieldName => $value) {
            $newFieldName = $this->processFieldName($fieldName, $action, $prefix);
            $newData[$newFieldName] = $value;
            unset($target[$fieldName]);
        }

        // then add all values with updated prefix
        foreach ($newData as $fieldName => $value) {
            $this->addField($target, $fieldName, $value);
        }

        return $target;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->getRenderingDefinition()->setLabel('Field Name Prefix');

        $schema->addProperty(static::KEY_PREFIX, new StringSchema(static::DEFAULT_PREFIX));

        $action = new StringSchema(static::DEFAULT_ACTION);
        $action->getAllowedValues()->addValue(static::ACTION_ADD);
        $action->getAllowedValues()->addValue(static::ACTION_REMOVE);
        $action->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $schema->addProperty(static::KEY_ACTION, $action);

        return $schema;
    }
}

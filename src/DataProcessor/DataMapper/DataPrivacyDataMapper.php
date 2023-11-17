<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareTrait;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DataPrivacyDataMapper extends DataMapper implements DataPrivacyManagerAwareInterface
{
    use DataPrivacyManagerAwareTrait;

    public const WEIGHT = 9999;

    // TODO use the same configuration as DataPrivacyValueModifier?

    public const KEY_LEVEL = 'level';

    public const DEFAULT_LEVEL = '';

    public const OPERATION_SET_NULL = 'setNull';

    public const OPERATION_MASK = 'mask';

    public const OPERATION_MASK_EXISTENCE = 'maskeExistence';

    public const KEY_OPERATION = 'operation';

    public const DEFAULT_OPERATION = self::OPERATION_SET_NULL;

    protected function operateOnValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        $operation = $this->getConfig(static::KEY_OPERATION);
        switch ($operation) {
            case static::OPERATION_SET_NULL:
                return null;
            case static::OPERATION_MASK:
                $value = (string)$value;
                if ($value !== '') {
                    $value = GeneralUtility::maskValue($value);
                }

                return $value;
            case static::OPERATION_MASK_EXISTENCE:
                if ((string)$value === '') {
                    return null;
                }

                return new BooleanValue(true);
            default:
                throw new DigitalMarketingFrameworkException(sprintf('unknown operation "%s"', $operation));
        }
    }

    public function mapData(DataInterface $target): DataInterface
    {
        $level = $this->getConfig(static::KEY_LEVEL);
        if (!$this->dataPrivacyManager->hasPermission($level)) {
            foreach ($target as $fieldName => $value) {
                $value = $this->operateOnValue($value);
                if ($value === null) {
                    unset($target[$fieldName]);
                } else {
                    $target[$fieldName] = $value;
                }
            }
        }

        return $target;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->getRenderingDefinition()->setSkipHeader(true);

        $schema->addProperty(static::KEY_LEVEL, new StringSchema(static::DEFAULT_LEVEL));

        $operationSchema = new StringSchema(static::DEFAULT_OPERATION);
        $operationSchema->getRenderingDefinition()->setLabel('Operation if not compliant');
        $operationSchema->getAllowedValues()->addValue(static::OPERATION_SET_NULL, 'Remove');
        $operationSchema->getAllowedValues()->addValue(static::OPERATION_MASK, 'Mask');
        $operationSchema->getAllowedValues()->addValue(static::OPERATION_MASK_EXISTENCE, 'Mask Existence');
        $schema->addProperty(static::KEY_OPERATION, $operationSchema);

        return $schema;
    }
}

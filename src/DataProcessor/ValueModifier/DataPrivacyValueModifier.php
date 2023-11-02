<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareTrait;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DataPrivacyValueModifier extends ValueModifier implements DataPrivacyManagerAwareInterface
{
    use DataPrivacyManagerAwareTrait;

    public const KEY_LEVEL = 'level';

    public const DEFAULT_LEVEL = '';

    public const OPERATION_SET_NULL = 'setNull';

    public const OPERATION_MASK = 'mask';

    public const OPERATION_MASK_EXISTENCE = 'maskExistence';

    public const KEY_OPERATION = 'operation';

    public const DEFAULT_OPERATION = self::OPERATION_SET_NULL;

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if ($value === null) {
            return null;
        }

        $level = $this->getConfig(static::KEY_LEVEL);
        if (!$this->dataPrivacyManager->hasPermission($level)) {
            $value = (string)$value;
            switch ($this->getConfig(static::KEY_OPERATION)) {
                case static::OPERATION_SET_NULL:
                    $value = null;
                    break;
                case static::OPERATION_MASK:
                    if ($value !== '') {
                        $value = GeneralUtility::maskValue($value);
                    }

                    break;
                case static::OPERATION_MASK_EXISTENCE:
                    $value = $value === '' ? null : new BooleanValue(true);
                    break;
            }
        }

        return $value;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
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

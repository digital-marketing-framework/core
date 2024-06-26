<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class SpliceValueModifier extends ValueModifier
{
    public const KEY_TOKEN = 'token';

    public const DEFAULT_TOKEN = '\\s';

    public const KEY_INDEX = 'index';

    public const DEFAULT_INDEX = '1';

    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        $token = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_TOKEN));
        $slice = $this->getConfig(static::KEY_INDEX);
        $indices = explode(':', (string)$slice);

        $offset = $indices[0] ?: 1;
        if ($offset > 0) {
            --$offset;
        }

        if (count($indices) === 1) {
            // '' || 'X'
            $length = 1;
        } else {
            // 'X:' || ':Y' || 'X:Y'
            $length = (int)$indices[1] ?: null;
        }

        $parts = $token === '' ? [$value] : explode($token, (string)$value);
        $slices = $length === null ? array_slice($parts, $offset) : array_slice($parts, $offset, $length);

        return implode($token, $slices);
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_TOKEN, new StringSchema(static::DEFAULT_TOKEN));
        $schema->addProperty(static::KEY_INDEX, new StringSchema(static::DEFAULT_INDEX));

        return $schema;
    }
}

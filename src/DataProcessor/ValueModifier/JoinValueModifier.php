<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class JoinValueModifier extends ValueModifier
{
    public const KEY_GLUE = 'glue';

    public const DEFAULT_GLUE = ',';

    public function modify(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if (!$this->proceed()) {
            return $value;
        }

        if ($value instanceof MultiValueInterface) {
            $glue = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_GLUE));
            $value->setGlue($glue);
            $value = (string)$value;
        }

        return $value;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_GLUE, new StringSchema(static::DEFAULT_GLUE));

        return $schema;
    }
}

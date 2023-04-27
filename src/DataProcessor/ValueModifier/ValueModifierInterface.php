<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

interface ValueModifierInterface extends DataProcessorPluginInterface
{
    public function modify(null|string|ValueInterface $value): null|string|ValueInterface;

    public static function getSchema(): SchemaInterface;
}

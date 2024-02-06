<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

interface ValueModifierInterface extends DataProcessorPluginInterface
{
    public function modify(string|ValueInterface|null $value): string|ValueInterface|null;

    public static function getSchema(): SchemaInterface;
}

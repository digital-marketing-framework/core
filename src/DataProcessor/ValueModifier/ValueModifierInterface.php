<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

interface ValueModifierInterface extends DataProcessorPluginInterface
{
    public function modify(string|ValueInterface|null $value): string|ValueInterface|null;

    public static function getSchema(): SchemaInterface;
}

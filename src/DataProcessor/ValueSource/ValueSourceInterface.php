<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

interface ValueSourceInterface extends DataProcessorPluginInterface
{
    public function build(): string|ValueInterface|null;

    public static function getSchema(): SchemaInterface;

    public static function modifiable(): bool;

    public static function canBeMultiValue(): bool;
}

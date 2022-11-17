<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

interface ContentResolverInterface extends ConfigurationResolverInterface
{
    public function build(): string|ValueInterface|null;
    public function finish(string|ValueInterface|null &$result): bool;
}

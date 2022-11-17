<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

abstract class ContentResolver extends ConfigurationResolver implements ContentResolverInterface
{
    protected const KEYWORD_GLUE = 'glue';

    protected static function getResolverInterface(): string
    {
        return ContentResolverInterface::class;
    }

    public function build(): string|ValueInterface|null
    {
        return null;
    }

    public function finish(string|ValueInterface|null &$result): bool
    {
        return false;
    }
}

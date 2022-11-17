<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

class IgnoreIfContentResolver extends IgnoreContentResolver
{
    protected const WEIGHT = 0;

    protected function ignore($result): bool
    {
        return $this->evaluate($this->configuration);
    }
}

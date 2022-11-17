<?php

namespace DigitalMarketingFramework\Core\Context;

trait ContextAwareTrait
{
    protected ContextInterface $context;

    public function setContext(ContextInterface $context): void
    {
        $this->context = $context;
    }
}

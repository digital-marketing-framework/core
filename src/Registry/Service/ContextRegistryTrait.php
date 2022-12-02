<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Context\ContextInterface;

trait ContextRegistryTrait
{
    protected ContextInterface $context;

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function setContext(ContextInterface $context): void
    {
        $this->context = $context;
    }
}

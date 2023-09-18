<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\RequestContext;

trait ContextRegistryTrait
{
    protected ContextInterface $context;

    public function getContext(): ContextInterface
    {
        if (!isset($this->context)) {
            $this->context = new RequestContext();
        }

        return $this->context;
    }

    public function setContext(ContextInterface $context): void
    {
        $this->context = $context;
    }
}

<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\ContextStackInterface;

trait ContextRegistryTrait
{
    public function getContext(): ContextStackInterface
    {
        return $this->getRegistryCollection()->getContext();
    }

    public function setContext(ContextInterface $context): void
    {
        $contextStack = $this->getContext();
        $contextStack->clearStack();
        $contextStack->pushContext($context);
    }

    public function pushContext(ContextInterface $context): void
    {
        $this->getContext()->pushContext($context);
    }

    public function popContext(): ?ContextInterface
    {
        return $this->getContext()->popContext();
    }
}

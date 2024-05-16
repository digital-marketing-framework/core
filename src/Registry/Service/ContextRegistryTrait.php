<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\ContextStack;
use DigitalMarketingFramework\Core\Context\ContextStackInterface;
use DigitalMarketingFramework\Core\Context\RequestContext;

trait ContextRegistryTrait
{
    protected ContextStackInterface $context;

    public function getContext(): ContextStackInterface
    {
        if (!isset($this->context)) {
            $this->context = new ContextStack();
            $this->context->pushContext(new RequestContext());
        }

        return $this->context;
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

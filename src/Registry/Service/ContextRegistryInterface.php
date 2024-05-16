<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\ContextStackInterface;

interface ContextRegistryInterface
{
    public function getContext(): ContextStackInterface;

    public function setContext(ContextInterface $request): void;

    public function pushContext(ContextInterface $context): void;

    public function popContext(): ?ContextInterface;
}

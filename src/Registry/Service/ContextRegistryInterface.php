<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Context\ContextInterface;

interface ContextRegistryInterface
{
    public function getContext(): ContextInterface;

    public function setContext(ContextInterface $request): void;
}

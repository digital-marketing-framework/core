<?php

namespace DigitalMarketingFramework\Core\Context;

interface ContextAwareInterface
{
    public function setContext(ContextInterface $context): void;
}

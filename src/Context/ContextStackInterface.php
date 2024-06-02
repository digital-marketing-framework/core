<?php

namespace DigitalMarketingFramework\Core\Context;

interface ContextStackInterface extends ContextInterface
{
    public function pushContext(ContextInterface $context): void;

    public function popContext(): ?ContextInterface;

    public function clearStack(): void;

    public function getDepth(): int;

    public function getActiveContext(): ContextInterface;
}

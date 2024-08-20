<?php

namespace DigitalMarketingFramework\Core\Context;

use BadMethodCallException;

class ContextStack implements ContextStackInterface
{
    /**
     * @param array<ContextInterface> $stack
     */
    public function __construct(
        protected array $stack = [],
    ) {
    }

    public function pushContext(ContextInterface $context): void
    {
        $this->stack[] = $context;
    }

    public function popContext(): ?ContextInterface
    {
        return array_pop($this->stack);
    }

    public function clearStack(): void
    {
        $this->stack = [];
    }

    public function getDepth(): int
    {
        return count($this->stack);
    }

    public function getActiveContext(): ContextInterface
    {
        $depth = $this->getDepth();
        if ($depth === 0) {
            throw new BadMethodCallException('Context stack is empty');
        }

        return $this->stack[$depth - 1];
    }

    public function toArray(): array
    {
        return $this->getActiveContext()->toArray();
    }

    public function getCookies(): array
    {
        return $this->getActiveContext()->getCookies();
    }

    public function getCookie(string $name): ?string
    {
        return $this->getActiveContext()->getCookie($name);
    }

    public function getIpAddress(): ?string
    {
        return $this->getActiveContext()->getIpAddress();
    }

    public function getTimestamp(): ?int
    {
        return $this->getActiveContext()->getTimestamp();
    }

    public function getRequestVariables(): array
    {
        return $this->getActiveContext()->getRequestVariables();
    }

    public function getRequestVariable(string $name): ?string
    {
        return $this->getActiveContext()->getRequestVariable($name);
    }

    public function getRequestArguments(): array
    {
        return $this->getActiveContext()->getRequestArguments();
    }

    public function getRequestArgument(string $name): ?string
    {
        return $this->getActiveContext()->getRequestArgument($name);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->getActiveContext()->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->getActiveContext()->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->getActiveContext()->offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->getActiveContext()->offsetUnset($offset);
    }
}

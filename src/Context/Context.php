<?php

namespace DigitalMarketingFramework\Core\Context;

use ArrayObject;

/**
 * @extends ArrayObject<string,mixed>
 */
abstract class Context extends ArrayObject implements ContextInterface
{
    /**
     * @param array<string,mixed> $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function getCookies(): array
    {
        return $this[static::KEY_COOKIES] ?? [];
    }

    public function getCookie(string $name): ?string
    {
        return $this->getCookies()[$name] ?? null;
    }

    public function getIpAddress(): ?string
    {
        return $this[static::KEY_IP_ADDRESS] ?? null;
    }

    public function getTimestamp(): ?int
    {
        return $this[static::KEY_TIMESTAMP] ?? null;
    }

    public function getRequestVariables(): array
    {
        return $this[static::KEY_REQUEST_VARIABLES] ?? [];
    }

    public function getRequestVariable(string $name): ?string
    {
        return $this->getRequestVariables()[$name] ?? null;
    }
}

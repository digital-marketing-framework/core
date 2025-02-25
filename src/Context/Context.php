<?php

namespace DigitalMarketingFramework\Core\Context;

use ArrayObject;
use BadMethodCallException;

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
        // the timestamp should always be present
        if (!isset($data[static::KEY_TIMESTAMP])) {
            $data[static::KEY_TIMESTAMP] = time();
        }

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

    public function getHost(): ?string
    {
        return $this[static::KEY_HOST] ?? null;
    }

    public function getUri(): ?string
    {
        return $this[static::KEY_URI] ?? null;
    }

    public function getReferer(): ?string
    {
        return $this[static::KEY_REFERER] ?? null;
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

    public function getRequestArguments(): array
    {
        return $this[static::KEY_REQUEST_ARGUMENTS] ?? [];
    }

    public function getRequestArgument(string $name): mixed
    {
        return $this->getRequestArguments()[$name] ?? null;
    }

    public function isResponsive(): bool
    {
        return false;
    }

    public function setResponseCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = true,
        bool $httponly = true,
    ): void {
        throw new BadMethodCallException('Generic context cannot set response cookies.');
    }

    public function getResponseData(): array
    {
        return [];
    }

    public function applyResponseData(): void
    {
        throw new BadMethodCallException('Generic context cannot apply response data.');
    }
}

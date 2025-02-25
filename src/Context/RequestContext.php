<?php

namespace DigitalMarketingFramework\Core\Context;

use BadMethodCallException;

class RequestContext extends Context
{
    protected int $timestamp;

    /**
     * @param array<string,mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->timestamp = time();
        parent::__construct($data);
    }

    public function toArray(): never
    {
        throw new BadMethodCallException('Request context is not supposed to be processed/saved as a whole');
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new BadMethodCallException('Request context is read-only and should not be attempted to be altered');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new BadMethodCallException('Request context is read-only and should not be attempted to be altered');
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->offsetGet($offset) !== null;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return match ($offset) {
            static::KEY_COOKIES => $this->getCookies(),
            static::KEY_IP_ADDRESS => $this->getIpAddress(),
            static::KEY_HOST => $this->getHost(),
            static::KEY_URI => $this->getUri(),
            static::KEY_REFERER => $this->getReferer(),
            static::KEY_REQUEST_VARIABLES => $this->getRequestVariables(),
            static::KEY_TIMESTAMP => $this->getTimestamp(),
            default => parent::offsetGet($offset),
        };
    }

    public function getCookies(): array
    {
        return $_COOKIE;
    }

    public function getIpAddress(): ?string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        }

        return $ip;
    }

    public function getHost(): ?string
    {
        return $this->getRequestVariables()['HTTP_HOST'] ?? null;
    }

    public function getUri(): ?string
    {
        return $this->getRequestVariables()['REQUEST_URI'] ?? null;
    }

    public function getReferer(): ?string
    {
        return $this->getRequestVariables()['HTTP_REFERER'] ?? null;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function getRequestVariables(): array
    {
        return $_SERVER;
    }

    public function getRequestArguments(): array
    {
        return $_GET;
    }
}

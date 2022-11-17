<?php

namespace DigitalMarketingFramework\Core\Context;

use BadMethodCallException;

class RequestContext extends Context
{
    public function toArray(): array
    {
        throw new BadMethodCallException('Request context is not supposed to be processed/saved as a whole');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('Request context is read-only and should not be attempted to be altered');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('Request context is read-only and should not be attempted to be altered');
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->offsetGet($offset) !== null;
    }

    public function offsetGet(mixed $offset): mixed
    {
        switch ((string)$offset) {
            case static::KEY_COOKIES:
                return $this->getCookies();
            case static::KEY_IP_ADDRESS:
                return $this->getIpAddress();
            case static::KEY_REQUEST_VARIABLES:
                return $this->getRequestVariables();
        }
        return parent::offsetGet($offset);
    }

    public function getCookies(): array
    {
        return $_COOKIE;
    }

    public function getIpAddress(): ?string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function getTimestamp(): ?int
    {
        return time();
    }

    public function getRequestVariables(): array
    {
        return $_SERVER;
    }
}

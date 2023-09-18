<?php

namespace DigitalMarketingFramework\Core\Context;

class WriteableContext extends Context implements WriteableContextInterface
{
    public function setCookie(string $name, string $value): void
    {
        $this[static::KEY_COOKIES][$name] = $value;
    }

    public function setIpAddress(string $ipAddress): void
    {
        $this[static::KEY_IP_ADDRESS] = $ipAddress;
    }

    public function setRequestVariable(string $name, string $value): void
    {
        $this[static::KEY_REQUEST_VARIABLES][$name] = $value;
    }

    public function setTimestamp(int $timestamp): void
    {
        $this[static::KEY_TIMESTAMP] = $timestamp;
    }

    public function copyFromContext(ContextInterface $context, string $key): bool
    {
        $value = $context[$key] ?? null;
        if ($value !== null) {
            $this[$key] = $value;

            return true;
        }

        return false;
    }

    public function copyCookieFromContext(ContextInterface $context, string $name): bool
    {
        $value = $context->getCookie($name);
        if ($value !== null) {
            $this->setCookie($name, $value);

            return true;
        }

        return false;
    }

    public function copyIpAddressFromContext(ContextInterface $context): bool
    {
        $value = $context->getIpAddress();
        if ($value !== null) {
            $this->setIpAddress($value);

            return true;
        }

        return false;
    }

    public function copyTimestampFromContext(ContextInterface $context): bool
    {
        $value = $context->getTimestamp();
        if ($value !== null) {
            $this->setTimestamp($value);

            return true;
        }

        return false;
    }

    public function copyRequestVariableFromContext(ContextInterface $context, string $name): bool
    {
        $value = $context->getRequestVariable($name);
        if ($value !== null) {
            $this->setRequestVariable($name, $value);

            return true;
        }

        return false;
    }
}

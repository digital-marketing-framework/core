<?php

namespace DigitalMarketingFramework\Core\Context;

class WriteableContext extends Context implements WriteableContextInterface
{
    protected bool $responsive = false;

    /** @var array<array{name:string,value:string,expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool}> */
    protected array $outgoingCookies = [];

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

    public function setResponsive(bool $responsive = true): void
    {
        $this->responsive = $responsive;
    }

    public function isResponsive(): bool
    {
        return $this->responsive;
    }

    public function setResponseCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = true,
        bool $httponly = true
    ): void {
        $this->setCookie($name, $value);
        if ($this->isResponsive()) {
            $this->outgoingCookies[$name] = [
                'name' => $name,
                'value' => $value,
                'expires' => $expires,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httponly,
            ];
        }
    }

    public function getResponseData(): array
    {
        return [
            'cookies' => $this->outgoingCookies,
        ];
    }

    public function applyResponseData(): void
    {
        foreach ($this->outgoingCookies as $cookie) {
            setcookie($cookie['name'], $cookie['value'], [
                'expires' => $cookie['expires'] ?? 0,
                'path' => $cookie['path'] ?? '/',
                'domain' => $cookie['domain'] ?? '',
                'secure' => $cookie['secure'],
                'httponly' => $cookie['httponly'],
            ]);
        }
    }
}

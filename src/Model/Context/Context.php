<?php

namespace DigitalMarketingFramework\Core\Model\Context;

use ArrayObject;

class Context extends ArrayObject implements ContextInterface
{
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    // namespace handling

    public function setInNamespace(string $namespace, string $name, string $value): void
    {
        $this[$namespace][$name] = $value;
    }

    public function addToNamespace(string $namespace, array $data): void
    {
        foreach ($data as $name => $value) {
            $this->setInNamespace($namespace, $name, $value);
        }
    }

    public function setNamespaceData(string $namespace, array $data): void
    {
        $this[$namespace] = $data;
    }

    public function getFromNamespace(string $namespace, string $name, $default = null): mixed
    {
        return $this[$namespace][$name] ?? $default;
    }

    public function getNamespaceData(string $namespace): array
    {
        return $this[$namespace] ?? [];
    }

    public function removeFromNamespace(string $namespace, string $name): void
    {
        if (isset($this[$namespace][$name])) {
            unset($this[$namespace][$name]);
        }
    }

    public function clearNamespace(string $namespace): void
    {
        $this[$namespace] = [];
    }

    // namespace "cookies"

    public function setCookie(string $name, string $value): void
    {
        $this->setInNamespace(static::NAMESPACE_COOKIES, $name, $value);
    }

    public function addCookies(array $cookies): void
    {
        $this->addToNamespace(static::NAMESPACE_COOKIES, $cookies);
    }

    public function setCookies(array $cookies): void
    {
        $this->setNamespaceData(static::NAMESPACE_COOKIES, $cookies);
    }

    public function getCookie(string $name, ?string $default = null): ?string
    {
        $value = $this->getFromNamespace(static::NAMESPACE_COOKIES, $name, $default);
        return $value !== null ? (string)$value : $value;
    }

    public function getCookies(): array
    {
        return $this->getNamespaceData(static::NAMESPACE_COOKIES);
    }

    public function removeCookie(string $name): void
    {
        $this->removeFromNamespace(static::NAMESPACE_COOKIES, $name);
    }

    public function clearCookies(): void
    {
        $this->clearNamespace(static::NAMESPACE_COOKIES);
    }

    // namespace "request_variables"

    public function setRequestVariable(string $name, string $value): void
    {
        $this->setInNamespace(static::NAMESPACE_REQUEST_VARIABLES, $name, $value);
    }

    public function addRequestVariables(array $requestVariables): void
    {
        $this->addToNamespace(static::NAMESPACE_REQUEST_VARIABLES, $requestVariables);
    }

    public function setRequestVariables(array $requestVariables): void
    {
        $this->setNamespaceData(static::NAMESPACE_REQUEST_VARIABLES, $requestVariables);
    }

    public function getRequestVariable(string $name, $default = null): mixed
    {
        return $this->getFromNamespace(static::NAMESPACE_REQUEST_VARIABLES, $name, $default);
    }

    public function getRequestVariables(): array
    {
        return $this->getNamespaceData(static::NAMESPACE_REQUEST_VARIABLES);
    }

    public function removeRequestVariable(string $name): void
    {
        $this->removeFromNamespace(static::NAMESPACE_REQUEST_VARIABLES, $name);
    }

    public function clearRequestVariables(): void
    {
        $this->clearNamespace(static::NAMESPACE_REQUEST_VARIABLES);
    }
}

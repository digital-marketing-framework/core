<?php

namespace DigitalMarketingFramework\Core\Model\Context;

use ArrayAccess;

interface ContextInterface extends ArrayAccess
{
    public const NAMESPACE_COOKIES = 'cookies';
    public const NAMESPACE_REQUEST_VARIABLES = 'request_variables';

    public function toArray(): array;

    public function setInNamespace(string $namespace, string $name, string $value): void;
    public function addToNamespace(string $namespace, array $data): void;
    public function setNamespaceData(string $namespace, array $data): void;
    public function getFromNamespace(string $namespace, string $name, $default = null): mixed;
    public function getNamespaceData(string $namespace): array;
    public function removeFromNamespace(string $namespace, string $name): void;
    public function clearNamespace(string $namespace): void;

    public function setCookie(string $name, string $value): void;
    public function addCookies(array $cookies): void;
    public function setCookies(array $cookies): void;
    public function getCookie(string $name, ?string $default = null): ?string;
    public function getCookies(): array;
    public function removeCookie(string $name): void;
    public function clearCookies(): void;

    public function setRequestVariable(string $name, string $value): void;
    public function addRequestVariables(array $requestVariables): void;
    public function setRequestVariables(array $requestVariables): void;
    public function getRequestVariable(string $name, mixed $default = null): mixed;
    public function getRequestVariables(): array;
    public function removeRequestVariable(string $name): void;
    public function clearRequestVariables(): void;
}

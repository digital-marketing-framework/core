<?php

namespace DigitalMarketingFramework\Core\Context;

interface WriteableContextInterface extends ContextInterface
{
    public function setCookie(string $name, string $value): void;

    public function setIpAddress(string $ipAddress): void;

    public function setTimestamp(int $timestamp): void;

    public function setRequestVariable(string $name, string $value): void;

    public function setRequestArgument(string $name, string $value): void;

    public function copyFromContext(ContextInterface $context, string $key): bool;

    public function copyCookieFromContext(ContextInterface $context, string $name): bool;

    public function copyIpAddressFromContext(ContextInterface $context): bool;

    public function copyTimestampFromContext(ContextInterface $context): bool;

    public function copyRequestVariableFromContext(ContextInterface $context, string $name): bool;

    public function copyRequestArgumentFromContext(ContextInterface $context, string $name): bool;

    public function setResponsive(bool $responsive = true): void;

    public function isResponsive(): bool;

    public function setResponseCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = true,
        bool $httponly = true
    ): void;

    /**
     * @return array<string,mixed>
     */
    public function getResponseData(): array;

    public function applyResponseData(): void;
}

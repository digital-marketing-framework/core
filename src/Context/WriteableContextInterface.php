<?php

namespace DigitalMarketingFramework\Core\Context;

interface WriteableContextInterface extends ContextInterface
{
    public function setCookie(string $name, string $value): void;

    public function setIpAddress(string $ipAddress): void;

    public function setTimestamp(int $timestamp): void;

    public function setRequestVariable(string $name, string $value): void;

    public function copyFromContext(ContextInterface $context, string $key): bool;

    public function copyCookieFromContext(ContextInterface $context, string $name): bool;

    public function copyIpAddressFromContext(ContextInterface $context): bool;

    public function copyTimestampFromContext(ContextInterface $context): bool;

    public function copyRequestVariableFromContext(ContextInterface $context, string $name): bool;
}

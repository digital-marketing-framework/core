<?php

namespace DigitalMarketingFramework\Core\Api\Request;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

interface ApiRequestInterface
{
    public function getApiVersion(): string;

    public function setApiVersion(string $apiVersion): void;

    public function getEndPoint(): EndPointInterface;

    public function setEndPoint(EndPointInterface $endPoint): void;

    public function getPath(): string;

    public function setPath(string $path): void;

    public function getMethod(): string;

    public function setMethod(string $method): void;

    /**
     * @return ?array<string,mixed>
     */
    public function getPayload(): ?array;

    /**
     * @param array<string,mixed> $payload
     */
    public function setPayload(array $payload): void;

    /**
     * @return ?array<string,mixed>
     */
    public function getContext(): ?array;

    /**
     * @param ?array<string,mixed> $context
     */
    public function setContext(?array $context): void;

    public function setVariable(string $key, string $value): void;

    public function getVariable(string $key): ?string;

    /**
     * @param array<string,string> $variables
     */
    public function addVariables(array $variables): void;

    /**
     * @return array<string,string>
     */
    public function getVariables(): array;
}

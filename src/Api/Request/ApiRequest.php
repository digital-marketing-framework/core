<?php

namespace DigitalMarketingFramework\Core\Api\Request;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

class ApiRequest implements ApiRequestInterface
{
    /** @var array<string,string> */
    protected array $variables = [];

    protected string $apiVersion = '';

    protected EndPointInterface $endPoint;

    /**
     * @param ?array<string,mixed> $payload
     * @param ?array<string,mixed> $context
     */
    public function __construct(
        protected string $path,
        protected string $method = 'GET',
        protected array $arguments = [],
        protected ?array $payload = null,
        protected ?array $context = null,
    ) {
        $this->path = trim($this->path, '/');
    }

    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    public function setApiVersion(string $apiVersion): void
    {
        $this->apiVersion = $apiVersion;
    }

    public function getEndPoint(): EndPointInterface
    {
        return $this->endPoint;
    }

    public function setEndPoint(EndPointInterface $endPoint): void
    {
        $this->endPoint = $endPoint;
    }

    public function setVariable(string $key, string $value): void
    {
        $this->variables[$key] = $value;
    }

    public function getVariable(string $key): ?string
    {
        return $this->variables[$key] ?? null;
    }

    public function addVariables(array $variables): void
    {
        foreach ($variables as $key => $value) {
            $this->variables[$key] = $value;
        }
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function setArgument(string $name, mixed $value): void
    {
        if ($value === null) {
            unset($this->arguments[$name]);
        } else {
            $this->arguments[$name] = $value;
        }
    }

    public function removeArgument(string $name): void
    {
        $this->setArgument($name, null);
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(?array $context): void
    {
        $this->context = $context;
    }
}

<?php

namespace DigitalMarketingFramework\Core\Api\Request;

class ApiRequest implements ApiRequestInterface
{
    /** @var array<string,string> */
    protected array $variables = [];

    /**
     * @param ?array<string,mixed> $payload
     * @param ?array<string,mixed> $context
     */
    public function __construct(
        protected string $path,
        protected string $method = 'GET',
        protected ?array $payload = null,
        protected ?array $context = null,
    ) {
        $this->path = trim($this->path, '/');
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

<?php

namespace DigitalMarketingFramework\Core\Backend\Response;

abstract class Response
{
    /**
     * @param array<string,string> $headers
     */
    public function __construct(
        protected string $content,
        protected int $statusCode = 200,
        protected array $headers = [],
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return array<string,string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }
}

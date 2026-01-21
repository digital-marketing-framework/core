<?php

namespace DigitalMarketingFramework\Core\Backend\Response;

abstract class Response
{
    /**
     * @param array<string,string> $headers
     * @param array<string,string> $scripts
     * @param array<string,string> $styles
     */
    public function __construct(
        protected string $content,
        protected int $statusCode = 200,
        protected array $headers = [],
        protected array $scripts = [],
        protected array $styles = [],
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

    public function removeHeader(string $name): void
    {
        unset($this->headers[$name]);
    }

    /**
     * @return array<string,string>
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function setScript(string $name, string $path): void
    {
        $this->scripts[$name] = $path;
    }

    public function removeScript(string $name): void
    {
        unset($this->scripts[$name]);
    }

    /**
     * @return array<string,string>
     */
    public function getStyleSheets(): array
    {
        return $this->styles;
    }

    public function setStyleSheet(string $name, string $path): void
    {
        $this->styles[$name] = $path;
    }

    public function removeStyleSheet(string $name): void
    {
        unset($this->styles[$name]);
    }
}

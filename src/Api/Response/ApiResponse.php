<?php

namespace DigitalMarketingFramework\Core\Api\Response;

class ApiResponse implements ApiResponseInterface
{
    /**
     * @param ?array<string,mixed> $data
     */
    public function __construct(
        protected ?array $data = null,
        protected int $statusCode = 200,
        protected ?string $message = null,
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getStatusMessage(): ?string
    {
        return $this->message;
    }

    public function getData(): array
    {
        $content = [
            'status' => [
                'code' => $this->getStatusCode(),
            ],
        ];

        if ($this->message !== null) {
            $content['status']['message'] = $this->message;
        }

        if ($this->data !== null) {
            $content['response'] = $this->data;
        }

        return $content;
    }

    public function getContent(): string
    {
        return json_encode($this->getData(), JSON_THROW_ON_ERROR);
    }
}

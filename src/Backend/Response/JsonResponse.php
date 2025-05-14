<?php

namespace DigitalMarketingFramework\Core\Backend\Response;

use JsonException;

class JsonResponse extends Response
{
    public function __construct(
        protected array $data,
        array $headers = ['Content-Type', 'application/json'],
    ) {
        [$content, $statusCode] = $this->convert($data);
        parent::__construct($content, $statusCode, $headers);
    }

    protected function convert(array $data): array
    {
        try {
            return [
                json_encode($data, JSON_THROW_ON_ERROR),
                200,
            ];
        } catch (JsonException $e) {
            return [
                $e->getMessage(),
                500,
            ];
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
        [$content, $statusCode] = $this->convert($data);
        $this->setStatusCode($statusCode);
        $this->setContent($content);
    }
}

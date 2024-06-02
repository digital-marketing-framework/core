<?php

namespace DigitalMarketingFramework\Core\Api\Response;

interface ApiResponseInterface
{
    public function getStatusCode(): int;

    public function getStatusMessage(): ?string;

    /**
     * @return array{status:array{code:int,message?:string},response?:array<string,mixed>}|array{resources:array<array<string,mixed>>}
     */
    public function getData(): array;

    public function getContent(): string;
}

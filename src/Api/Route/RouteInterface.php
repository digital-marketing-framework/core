<?php

namespace DigitalMarketingFramework\Core\Api\Route;

interface RouteInterface
{
    public function getId(): string;

    /**
     * @return bool|array<string,string>
     */
    public function matchPath(string $path): bool|array;

    /**
     * @return array<string,string>
     */
    public function getConstants(): array;

    /**
     * @return array<string,array<string,mixed>>
     */
    public function getFormats(): array;

    /**
     * @return array<string>
     */
    public function getMethods(): array;

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array;
}

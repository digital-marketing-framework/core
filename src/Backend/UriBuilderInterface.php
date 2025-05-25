<?php

namespace DigitalMarketingFramework\Core\Backend;

interface UriBuilderInterface
{
    /**
     * @param array<string,mixed> $arguments
     */
    public function build(string $route, array $arguments = []): string;
}

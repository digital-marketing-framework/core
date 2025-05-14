<?php

namespace DigitalMarketingFramework\Core\Backend;

interface UriBuilderInterface
{
    public function build(string $route, array $arguments = []): string;
}

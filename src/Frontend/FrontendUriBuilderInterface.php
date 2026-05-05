<?php

namespace DigitalMarketingFramework\Core\Frontend;

interface FrontendUriBuilderInterface
{
    public function build(string $uri): string;
}

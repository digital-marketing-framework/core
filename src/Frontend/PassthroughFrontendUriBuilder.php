<?php

namespace DigitalMarketingFramework\Core\Frontend;

class PassthroughFrontendUriBuilder implements FrontendUriBuilderInterface
{
    public function build(string $uri): string
    {
        return $uri;
    }
}

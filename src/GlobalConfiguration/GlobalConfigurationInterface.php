<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

interface GlobalConfigurationInterface
{
    public function get(string $key, mixed $default = null): mixed;
}

<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryTrait;

class ConfigurationResolverRegistry extends Registry implements ConfigurationResolverRegistryInterface
{
    use ConfigurationResolverRegistryTrait;
}

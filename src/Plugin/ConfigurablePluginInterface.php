<?php

namespace DigitalMarketingFramework\Core\Plugin;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

interface ConfigurablePluginInterface extends PluginInterface
{
    public function setDefaultConfiguration(array $defaultConfiguration): void;
    public function getDefaultConfiguration(): array;
    public static function getSchema(): SchemaInterface;
}

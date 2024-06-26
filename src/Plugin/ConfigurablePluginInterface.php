<?php

namespace DigitalMarketingFramework\Core\Plugin;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

interface ConfigurablePluginInterface extends PluginInterface
{
    /**
     * @param array<string,mixed> $defaultConfiguration
     */
    public function setDefaultConfiguration(array $defaultConfiguration): void;

    /**
     * @return array<string,mixed>
     */
    public function getDefaultConfiguration(): array;

    public static function getSchema(): SchemaInterface;

    public static function getLabel(): ?string;
}

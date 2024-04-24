<?php

namespace DigitalMarketingFramework\Core\Plugin;

interface PluginInterface
{
    public function getKeyword(): string;

    /**
     * @return int The generic, initial weight of the plugin, hard-coded
     */
    public static function getWeight(): int;

    /**
     * @return int The configured weight of the plugin, may be different from the initial weight
     */
    public function getConfiguredWeight(): int;
}

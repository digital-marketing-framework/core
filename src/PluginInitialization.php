<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryInterface;

abstract class PluginInitialization
{
    protected const PLUGINS = [];

    public static function initialize(PluginRegistryInterface $registry): void
    {
        foreach (static::PLUGINS as $interface => $plugins) {
            foreach ($plugins as $keyword => $class) {
                $registry->registerPlugin($interface, $class, [], $keyword);
            }
        }
    }
}

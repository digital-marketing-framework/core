<?php

namespace DigitalMarketingFramework\Core\Plugin;

interface PluginInterface
{
    public function getKeyword(): string;

    public static function getWeight(): int;
}

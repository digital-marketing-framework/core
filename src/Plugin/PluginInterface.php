<?php

namespace DigitalMarketingFramework\Core\Plugin;

interface PluginInterface
{
    public function getKeyword(): string;
    public function getWeight(): int;
}

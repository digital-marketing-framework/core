<?php

namespace DigitalMarketingFramework\Core\Cleanup;

use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface CleanupTaskInterface extends PluginInterface
{
    public function execute(): void;
}

<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;

interface ComparisonInterface extends DataProcessorPluginInterface
{
    public function compare(): bool;
}

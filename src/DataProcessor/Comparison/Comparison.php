<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;

abstract class Comparison extends DataProcessorPlugin implements ComparisonInterface
{
    public const KEY_ANY_ALL = 'anyAll';
    public const DEFAULT_ANY_ALL = 'any';

    abstract public function compare(): bool;

    public static function handleMultiValuesIndividually(): bool
    {
        return true;
    }

    protected function compareAnyEmpty(): bool
    {
        return true;
    }

    protected function compareAllEmpty(): bool
    {
        return true;
    }

    public static function getDefaultConfiguration(): array
    {
        $config = parent::getDefaultConfiguration();
        if (static::handleMultiValuesIndividually()) {
            $config[static::KEY_ANY_ALL] = static::DEFAULT_ANY_ALL;
        }
        return $config;
    }
}

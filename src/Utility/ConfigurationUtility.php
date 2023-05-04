<?php

namespace DigitalMarketingFramework\Core\Utility;

final class ConfigurationUtility
{
    public static function mergeConfiguration(array $target, array $source, bool $resolveNull = true): array
    {
        foreach ($source as $key => $value) {
            if (!array_key_exists($key, $target)) {
                if (!$resolveNull || $value !== null) {
                    $target[$key] = $value;
                }
            } elseif (is_array($value) && is_array($target[$key])) {
                $target[$key] = static::mergeConfiguration($target[$key], $value, $resolveNull);
            } elseif ($resolveNull && $value === null) {
                unset($target[$key]);
            } else {
                $target[$key] = $value;
            }
        }
        return $target;
    }

    public static function resolveNullInMergedConfiguration(array $configuration): array
    {
        return static::mergeConfiguration($configuration, $configuration, true);
    }
}

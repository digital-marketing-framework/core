<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

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
            } elseif (is_array($value)) {
                if ($target[$key] === null) {
                    $target[$key] = $value;
                } else {
                    $target[$key] = static::mergeConfiguration([ConfigurationInterface::KEY_SELF => $target[$key]], $value, $resolveNull);
                }
            } elseif (is_array($target[$key])) {
                if ($value === null) {
                    if ($resolveNull) {
                        unset($target[$key]);
                    } else {
                        $target[$key] = $value;
                    }
                } else {
                    $target[$key] = static::mergeConfiguration($target[$key], [ConfigurationInterface::KEY_SELF => $value], $resolveNull);
                }
            } else {
                if ($resolveNull && $value === null) {
                    unset($target[$key]);
                } else {
                    $target[$key] = $value;
                }
            }
        }
        return $target;
    }

    public static function resolveNullInMergedConfiguration(array $configuration): array
    {
        return static::mergeConfiguration($configuration, $configuration, true);
    }
}

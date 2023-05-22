<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

final class ConfigurationUtility
{
    const MERGE_EXCLUDE_FIELDS = ['name', 'includes'];

    public static function mergeConfiguration(array $target, array $source, bool $resolveNull = true): array
    {
        foreach ($source as $key => $value) {
            if (in_array($key, static::MERGE_EXCLUDE_FIELDS)) {
                continue;
            }
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

    public static function mergeConfigurationStack(array $configurationStack, bool $resolveNull = true): array
    {
        $result = [];
        foreach ($configurationStack as $configuration) {
            $result = static::mergeConfiguration($result, $configuration, resolveNull:false);
        }
        if ($resolveNull) {
            $result = static::resolveNullInMergedConfiguration($result);
        }
        $lastConfiguration = $configurationStack[count($configurationStack) - 1];
        foreach (static::MERGE_EXCLUDE_FIELDS as $mergeExcludeField) {
            if (isset($lastConfiguration[$mergeExcludeField])) {
                $result[$mergeExcludeField] = $lastConfiguration[$mergeExcludeField];
            }
        }
        return $result;
    }

    public static function splitConfiguration(array $parentConfiguration, array $mergedConfiguration): array
    {
        $splitConfiguration = [];
        foreach ($mergedConfiguration as $key => $value) {
            if (in_array($key, static::MERGE_EXCLUDE_FIELDS)) {
                $splitConfiguration[$key] = $value;
                continue;
            }
            if (!array_key_exists($key, $parentConfiguration)) {
                $splitConfiguration[$key] = $value;
            } elseif (is_array($value) && is_array($parentConfiguration[$key])) {
                $splitSubConfiguration = static::splitConfiguration($parentConfiguration[$key], $value);
                if (!empty($splitSubConfiguration)) {
                    $splitConfiguration[$key] = $splitSubConfiguration;
                }
            } else if (is_array($value) || is_array($parentConfiguration[$key])) {
                throw new DigitalMarketingFrameworkException(sprintf('config:split found inconsistent structure for key "%s"', $key));
            } else if ($value !== $parentConfiguration[$key]) {
                $splitConfiguration[$key] = $value;
            }
        }
        foreach ($parentConfiguration as $key => $value) {
            if (in_array($key, static::MERGE_EXCLUDE_FIELDS)) {
                continue;
            }
            if (!array_key_exists($key, $mergedConfiguration)) {
                $splitConfiguration[$key] = null;
            }
        }
        return $splitConfiguration;
    }
}

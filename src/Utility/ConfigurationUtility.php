<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

final class ConfigurationUtility
{
    public const MERGE_EXCLUDE_FIELDS = ['metaData'];

    /**
     * @param array<string,mixed> $target
     * @param array<string,mixed> $source
     * @param array<string> $excludeKeys
     *
     * @return array<string,mixed>
     */
    public static function mergeConfiguration(array $target, array $source, bool $resolveNull = true, array $excludeKeys = self::MERGE_EXCLUDE_FIELDS): array
    {
        foreach ($source as $key => $value) {
            if (in_array($key, $excludeKeys)) {
                continue;
            }

            if (!array_key_exists($key, $target)) {
                if (!$resolveNull || $value !== null) {
                    $target[$key] = $value;
                }
            } elseif (is_array($value) && is_array($target[$key])) {
                $target[$key] = self::mergeConfiguration($target[$key], $value, $resolveNull, $excludeKeys);
            } elseif ($resolveNull && $value === null) {
                unset($target[$key]);
            } else {
                $target[$key] = $value;
            }
        }

        return $target;
    }

    /**
     * @param array<string,mixed> $configuration
     * @param array<string> $excludeKeys
     *
     * @return array<string,mixed>
     */
    public static function resolveNullInMergedConfiguration(array $configuration, array $excludeKeys = self::MERGE_EXCLUDE_FIELDS): array
    {
        return self::mergeConfiguration($configuration, $configuration, true, $excludeKeys);
    }

    /**
     * @param array<array<string,mixed>> $configurationStack
     * @param array<string> $excludeKeys
     *
     * @return array<string,mixed>
     */
    public static function mergeConfigurationStack(array $configurationStack, bool $resolveNull = true, array $excludeKeys = self::MERGE_EXCLUDE_FIELDS): array
    {
        $configurationStack = array_values($configurationStack);
        $result = [];
        foreach ($configurationStack as $configuration) {
            $result = self::mergeConfiguration($result, $configuration, resolveNull: false, excludeKeys: $excludeKeys);
        }

        if ($resolveNull) {
            $result = self::resolveNullInMergedConfiguration($result, $excludeKeys);
        }

        $lastConfiguration = $configurationStack[count($configurationStack) - 1];
        foreach ($excludeKeys as $mergeExcludeField) {
            if (isset($lastConfiguration[$mergeExcludeField])) {
                $result[$mergeExcludeField] = $lastConfiguration[$mergeExcludeField];
            }
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $parentConfiguration
     * @param array<string,mixed> $mergedConfiguration
     * @param array<string> $excludeKeys
     *
     * @return array<string,mixed>
     */
    public static function splitConfiguration(array $parentConfiguration, array $mergedConfiguration, array $excludeKeys = self::MERGE_EXCLUDE_FIELDS): array
    {
        $splitConfiguration = [];
        foreach ($mergedConfiguration as $key => $value) {
            if (in_array($key, $excludeKeys)) {
                $splitConfiguration[$key] = $value;
                continue;
            }

            if (!array_key_exists($key, $parentConfiguration)) {
                $splitConfiguration[$key] = $value;
            } elseif (is_array($value) && is_array($parentConfiguration[$key])) {
                $splitSubConfiguration = self::splitConfiguration($parentConfiguration[$key], $value, $excludeKeys);
                if ($splitSubConfiguration !== []) {
                    $splitConfiguration[$key] = $splitSubConfiguration;
                }
            } elseif (is_array($value) || is_array($parentConfiguration[$key])) {
                throw new DigitalMarketingFrameworkException(sprintf('config:split found inconsistent structure for key "%s"', $key));
            } elseif ($value !== $parentConfiguration[$key]) {
                $splitConfiguration[$key] = $value;
            }
        }

        foreach (array_keys($parentConfiguration) as $key) {
            if (in_array($key, $excludeKeys)) {
                continue;
            }

            if (!array_key_exists($key, $mergedConfiguration)) {
                $splitConfiguration[$key] = null;
            }
        }

        return $splitConfiguration;
    }

    public static function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * @param array<string> $additionalKeywords
     */
    public static function generateUuidForPackage(string $package, string $keyword, array $additionalKeywords = []): string
    {
        $parts = ['pkg', $package, $keyword, ...$additionalKeywords];

        return implode('--', $parts);
    }
}

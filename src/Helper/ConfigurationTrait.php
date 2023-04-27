<?php

namespace DigitalMarketingFramework\Core\Helper;

trait ConfigurationTrait
{
    protected array $configuration;

    abstract public static function getDefaultConfiguration(): array;

    protected function getConfig(string $key, $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): mixed
    {
        if ($default === null) {
            $defaultConfiguration = $defaultConfiguration ?? static::getDefaultConfiguration();
            if (array_key_exists($key, $defaultConfiguration)) {
                $default = $defaultConfiguration[$key];
            }
        }
        $configuration = $configuration ?? $this->configuration;
        if (is_array($configuration) && array_key_exists($key, $configuration)) {
            return $configuration[$key];
        }
        return $default;
    }
}

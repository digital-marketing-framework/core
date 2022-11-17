<?php

namespace DigitalMarketingFramework\Core\Helper;

trait ConfigurationTrait
{
    protected mixed $configuration;

    abstract public static function getDefaultConfiguration(): array;

    protected function getConfig(string $key, $default = null)
    {
        if ($default === null) {
            $defaults = static::getDefaultConfiguration();
            if (array_key_exists($key, $defaults)) {
                $default = $defaults[$key];
            }
        }
        if (is_array($this->configuration) && array_key_exists($key, $this->configuration)) {
            return $this->configuration[$key];
        }
        return $default;
    }
}

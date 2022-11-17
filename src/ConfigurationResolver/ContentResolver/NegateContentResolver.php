<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationBehaviour;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class NegateContentResolver extends ContentResolver
{
    protected const WEIGHT = 101;

    protected const KEY_TRUE = 'true';
    protected const DEFAULT_TRUE = null;
    protected const FALLBACK_TRUE = '1';

    protected const KEY_FALSE = 'false';
    protected const DEFAULT_FALSE = null;
    protected const FALLBACK_FALSE = '0';

    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::ConvertScalarToArrayWithSelfValue;
    }

    protected function negateValue($value, $true, $false)
    {
        if ($value instanceof BooleanValue) {
            if ($value->getValue() && $false !== null) {
                return $false;
            } elseif (!$value->getValue() && $true !== null) {
                return $true;
            }
            return $value->negated();
        } else {
            if ($true === null) {
                $true = static::FALLBACK_TRUE;
            }
            if ($false === null) {
                $false = static::FALLBACK_FALSE;
            }
            if ($value === $true) {
                return $false;
            } elseif ($value === $false) {
                return $true;
            }
            return (bool)$value ? $false : $true;
        }
    }

    public function finish(&$result): bool
    {
        $enabled = $this->configuration[ConfigurationResolverInterface::KEY_SELF] ?? true;
        if ($enabled && $result !== null) {
            $true = $this->resolveContent($this->getConfig(static::KEY_TRUE));
            $false = $this->resolveContent($this->getConfig(static::KEY_FALSE));
            if ($result instanceof MultiValue) {
                foreach ($result as $key => $value) {
                    $result[$key] = $this->negateValue($value, $true, $false);
                }
            } else {
                $result = $this->negateValue($result, $true, $false);
            }
        }
        return false;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_TRUE => static::DEFAULT_TRUE,
            static::KEY_FALSE => static::DEFAULT_FALSE,
        ];
    }
}

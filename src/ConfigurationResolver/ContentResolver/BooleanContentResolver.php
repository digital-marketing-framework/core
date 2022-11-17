<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationBehaviour;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class BooleanContentResolver extends ContentResolver
{
    protected const KEY_VALUE = 'value';
    protected const DEFAULT_VALUE = null;
    
    protected const KEY_TRUE = 'true';
    protected const DEFAULT_TRUE = '1';

    protected const KEY_FALSE = 'false';
    protected const DEFAULT_FALSE = '0';

    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::ConvertScalarToArrayWithSelfValue;
    }

    public function build(): string|ValueInterface|null
    {
        $value = $this->resolveContent($this->configuration[static::KEY_VALUE] ?? $this->configuration[ConfigurationResolverInterface::KEY_SELF]);
        if ($value === null) {
            return null;
        }

        $true = isset($this->configuration[static::KEY_TRUE]) ? $this->resolveContent($this->configuration[static::KEY_TRUE]) : null;
        if ($true === null) {
            $true = static::DEFAULT_TRUE;
        }
        
        $false = isset($this->configuration[static::KEY_FALSE]) ? $this->resolveContent($this->configuration[static::KEY_FALSE]) : null;
        if ($false === null) {
            $false = static::DEFAULT_FALSE;
        }

        return new BooleanValue((bool)$value, $true, $false);
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_VALUE => static::DEFAULT_VALUE,
            static::KEY_TRUE => static::DEFAULT_TRUE,
            static::KEY_FALSE => static::DEFAULT_FALSE,
        ];
    }
}

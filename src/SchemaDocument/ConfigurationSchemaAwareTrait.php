<?php

namespace DigitalMarketingFramework\Core\SchemaDocument;

use DigitalMarketingFramework\Core\Registry\Service\ConfigurationSchemaRegistryInterface;

/** @phpstan-ignore-next-line This trait can be used by other packages, even though it is not used in this one. */
trait ConfigurationSchemaAwareTrait
{
    protected ConfigurationSchemaRegistryInterface $configurationSchemaProvider;

    public function setConfigurationSchemaProvider(ConfigurationSchemaRegistryInterface $provider): void
    {
        $this->configurationSchemaProvider = $provider;
    }

    protected function getConfigurationSchemaDocument(): SchemaDocument
    {
        return $this->configurationSchemaProvider->getConfigurationSchemaDocument();
    }
}

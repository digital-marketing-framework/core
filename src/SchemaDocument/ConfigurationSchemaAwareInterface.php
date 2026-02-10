<?php

namespace DigitalMarketingFramework\Core\SchemaDocument;

use DigitalMarketingFramework\Core\Registry\Service\ConfigurationSchemaRegistryInterface;

/**
 * Provides access to the configuration SchemaDocument via a provider interface.
 *
 * Unlike other Aware interfaces that inject a ready-to-use object, this interface
 * injects a provider (ConfigurationSchemaRegistryInterface) and the trait exposes
 * a getConfigurationSchemaDocument() method that fetches the schema on-demand.
 *
 * This pattern is necessary because the SchemaDocument is built lazily and may be
 * incomplete during initialization phases. By deferring access to runtime (when
 * getConfigurationSchemaDocument() is actually called), we ensure the schema is
 * fully built.
 *
 * IMPORTANT: Only call getConfigurationSchemaDocument() during runtime operations
 * (e.g., processing requests, handling jobs), never during initialization phases.
 */
interface ConfigurationSchemaAwareInterface
{
    public function setConfigurationSchemaProvider(ConfigurationSchemaRegistryInterface $provider): void;
}

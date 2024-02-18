<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

interface ConfigurationEditorControllerInterface
{
    /**
     * @return array<string,mixed>
     */
    public function getDefaultConfiguration(): array;

    public function getSchemaDocument(): SchemaDocument;

    /**
     * @return array{valueSets:array<string,array<string,string>>,types:array<string,array<string,mixed>>,schema:array<string,mixed>}}
     */
    public function getSchemaDocumentAsArray(): array;

    /**
     * @param array<string,mixed> $configuration
     *
     * @return array<string,mixed>
     */
    public function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array;

    /**
     * @param array<string,mixed> $mergedConfiguration
     *
     * @return array<string,mixed>
     */
    public function splitConfiguration(array $mergedConfiguration): array;

    /**
     * @param array<string,mixed> $referenceMergedConfiguration
     * @param array<string,mixed> $mergedConfiguration
     *
     * @return array<string,mixed>
     */
    public function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array;
}

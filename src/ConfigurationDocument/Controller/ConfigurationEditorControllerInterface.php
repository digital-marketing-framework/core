<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Controller;

use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface ConfigurationEditorControllerInterface
{
    /**
     * @return array<string,mixed>
     */
    public function getDefaultConfiguration(): array;

    public function preSaveDataTransform(mixed &$data): void;

    public function convertValueTypes(mixed &$data): void;

    public function getSchemaDocument(): SchemaDocument;

    public function setSchemaDocument(SchemaDocument $schemaDocument): void;

    /**
     * @return array{valueSets:array<string,array<string,string>>,types:array<string,array<string,mixed>>,schema:array<string,mixed>}}
     */
    public function getSchemaDocumentAsArray(): array;

    /**
     * @return array<mixed>
     */
    public function parseDocument(string $document): array;

    /**
     * @param array<mixed> $configuration
     */
    public function produceDocument(array $configuration): string;

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

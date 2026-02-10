<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Migration;

interface ConfigurationDocumentMigrationInterface
{
    public function getKey(): string;

    public function getSourceVersion(): string;

    public function getTargetVersion(): string;

    /**
     * @param array<string,mixed> $delta The document's stored configuration (delta) to transform
     * @param MigrationContext $context Provides merged parent configurations for decision-making
     *
     * @return array<string,mixed> The transformed delta
     */
    public function migrate(array $delta, MigrationContext $context): array;

    public function checkVersions(): bool;
}

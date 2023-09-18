<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Migration;

interface ConfigurationDocumentMigrationInterface
{
    public function getKey(): string;

    public function getSourceVersion(): string;

    public function getTargetVersion(): string;

    /**
     * @param array<mixed> $configuration
     *
     * @return array<mixed>
     */
    public function migrate(array $configuration): array;

    public function checkVersions(): bool;
}

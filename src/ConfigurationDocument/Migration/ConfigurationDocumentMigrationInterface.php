<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Migration;

interface ConfigurationDocumentMigrationInterface
{
    public function getKey(): string;
    public function getSourceVersion(): string;
    public function getTargetVersion(): string;
    public function migrate(array $configuration): array;
    public function checkVersions(): bool;
}

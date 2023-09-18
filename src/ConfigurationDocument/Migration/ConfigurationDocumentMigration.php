<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Migration;

abstract class ConfigurationDocumentMigration implements ConfigurationDocumentMigrationInterface
{
    abstract public function getKey(): string;

    abstract public function getSourceVersion(): string;

    abstract public function getTargetVersion(): string;

    abstract public function migrate(array $configuration): array;

    protected function checkVersion(string $version): bool
    {
        return preg_match('/\d+(\.\d+)*/', $version);
    }

    public function checkVersions(): bool
    {
        $sourceVersion = $this->getSourceVersion();
        if ($sourceVersion !== '' && !$this->checkVersion($sourceVersion)) {
            return false;
        }

        $targetVersion = $this->getTargetVersion();
        if (!$this->checkVersion($targetVersion)) {
            return false;
        }

        $sourceParts = $sourceVersion === '' ? explode('.', $sourceVersion) : [];
        $targetParts = $targetVersion === '' ? explode('.', $targetVersion) : [];

        while (count($sourceParts) < count($targetParts)) {
            $sourceParts[] = '0';
        }

        while (count($sourceParts) > count($targetParts)) {
            $targetParts[] = '0';
        }

        while ($sourceParts !== []) {
            $source = array_shift($sourceParts);
            $target = array_shift($targetParts);
            if ($source > $target) {
                // source:1.2.3 > target:1.1.4
                return false;
            } elseif ($source < $target) {
                // source:1.2.3 < target:1.3.2
                return true;
            }
        }

        // source:1.2.3 == target:1.2.3
        return false;
    }
}

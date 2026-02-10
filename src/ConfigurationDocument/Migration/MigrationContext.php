<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Migration;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;

class MigrationContext
{
    /** @var array<string,mixed>|null */
    protected ?array $parentConfiguration = null;

    /** @var array<string,mixed>|null */
    protected ?array $parentConfigurationWithDefaults = null;

    public const STATUS_TAG_ONLY = 'tagOnly';

    public const STATUS_GENUINE = 'genuine';

    public const STATUS_ERROR = 'error';

    /** @var array<string, array{from: string, to: string, status: string, message: string}> Per-key migration results */
    protected array $migrationInfo = [];

    /**
     * @param array<array<string,mixed>> $parentStack
     * @param array<string,mixed> $sysDefaults
     */
    public function __construct(
        protected array $parentStack,
        protected array $sysDefaults,
    ) {
    }

    /**
     * Record the migration result for a single key (package).
     *
     * Called by the migration service after processing each key's migration chain.
     *
     * @param string $from Raw version tag from the document (empty string if not set)
     * @param string $to Target version from the schema
     */
    public function recordMigrationResult(string $key, string $from, string $to, string $status, string $message = ''): void
    {
        $this->migrationInfo[$key] = ['from' => $from, 'to' => $to, 'status' => $status, 'message' => $message];
    }

    /**
     * @return array<string, array{from: string, to: string, status: string, message: string}>
     */
    public function getMigrationInfo(): array
    {
        return $this->migrationInfo;
    }

    /**
     * Whether any key had genuine data changes (not just version tag updates).
     */
    public function hasGenuineChanges(): bool
    {
        foreach ($this->migrationInfo as $info) {
            if ($info['status'] === self::STATUS_GENUINE) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether any key had a migration error.
     */
    public function hasErrors(): bool
    {
        foreach ($this->migrationInfo as $info) {
            if ($info['status'] === self::STATUS_ERROR) {
                return true;
            }
        }

        return false;
    }

    /**
     * Merged parent configuration without SYS:defaults.
     *
     * @return array<string,mixed>
     */
    public function getParentConfiguration(): array
    {
        if ($this->parentConfiguration === null) {
            $this->parentConfiguration = $this->parentStack !== []
                ? ConfigurationUtility::mergeConfigurationStack($this->parentStack)
                : [];
        }

        return $this->parentConfiguration;
    }

    /**
     * Merged parent configuration with SYS:defaults.
     *
     * @return array<string,mixed>
     */
    public function getParentConfigurationWithDefaults(): array
    {
        if ($this->parentConfigurationWithDefaults === null) {
            $stack = [$this->sysDefaults, ...$this->parentStack];
            $this->parentConfigurationWithDefaults = ConfigurationUtility::mergeConfigurationStack($stack);
        }

        return $this->parentConfigurationWithDefaults;
    }

    /**
     * Effective configuration: merged parents + delta, without SYS:defaults.
     *
     * @param array<string,mixed> $delta
     *
     * @return array<string,mixed>
     */
    public function getEffectiveConfiguration(array $delta): array
    {
        $stack = [...$this->parentStack, $delta];

        return ConfigurationUtility::mergeConfigurationStack($stack);
    }

    /**
     * Effective configuration: merged parents + delta, with SYS:defaults.
     *
     * @param array<string,mixed> $delta
     *
     * @return array<string,mixed>
     */
    public function getEffectiveConfigurationWithDefaults(array $delta): array
    {
        $stack = [$this->sysDefaults, ...$this->parentStack, $delta];

        return ConfigurationUtility::mergeConfigurationStack($stack);
    }

    /**
     * Raw parent stack including SYS:defaults.
     *
     * Returns an ordered array from root (SYS:defaults) to closest parent.
     *
     * @return array<array<string,mixed>>
     */
    public function getRawParentStack(): array
    {
        return [$this->sysDefaults, ...$this->parentStack];
    }

    /**
     * Extract the version tag for a given migration key from a configuration.
     *
     * @param array<string,mixed> $configuration A configuration (delta or parent layer)
     * @param string $key The migration key (package identifier)
     *
     * @return string The version tag, or INITIAL_VERSION if not set
     */
    public function getVersion(array $configuration, string $key): string
    {
        return $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION][$key] ?? SchemaDocument::INITIAL_VERSION;
    }

    /**
     * Split a merged configuration against the parent configuration to extract the delta.
     * Useful for migrations that merge, transform, then split to get a clean delta.
     *
     * @param array<string,mixed> $mergedConfiguration
     *
     * @return array<string,mixed>
     */
    public function splitConfiguration(array $mergedConfiguration): array
    {
        return ConfigurationUtility::splitConfiguration(
            $this->getParentConfiguration(),
            $mergedConfiguration
        );
    }
}

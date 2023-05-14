<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;

abstract class ConfigurationDocumentStorage implements ConfigurationDocumentStorageInterface, GlobalConfigurationAwareInterface, LoggerAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use LoggerAwareTrait;

    public const CONFIGURATION_KEY = 'digitalmarketingframework';
    public const STORAGE_CONFIGURATION_KEY = 'configurationStorage';

    abstract public function getDocumentIdentifiers(): array;
    abstract public function getDocument(string $documentIdentifier): string;
    abstract public function setDocument(string $documentIdentifier, string $document): void;
    abstract public function isReadOnly(string $identifier): bool;
    protected function getStorageConfiguration(?string $key = null, mixed $default = null): mixed
    {
        $config = $this->globalConfiguration->get(static::CONFIGURATION_KEY)[static::STORAGE_CONFIGURATION_KEY] ?? [];
        if ($key !== null) {
            return $config[$key] ?? $default;
        }
        return $config;
    }

    public function initalizeConfigurationDocumentStorage(): void
    {
    }
}

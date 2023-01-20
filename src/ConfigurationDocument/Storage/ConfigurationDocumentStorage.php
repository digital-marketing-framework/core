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

    abstract public function getDocumentIdentifiers(): array;
    abstract public function getDocument(string $documentIdentifier): string;
    abstract public function setDocument(string $documentIdentifier, string $document): void;
    abstract public function isReadOnly(string $identifier): bool;
    abstract public function initalizeConfigurationDocumentStorage(): void;
}

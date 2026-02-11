<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Discovery;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\ConfigurationStorageSettings;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Resource\ResourceServiceInterface;

class StaticResourceConfigurationDocumentDiscovery implements StaticConfigurationDocumentDiscoveryInterface, GlobalConfigurationAwareInterface, LoggerAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use LoggerAwareTrait;

    protected ConfigurationStorageSettings $configurationStorageSettings;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    protected function getConfigurationStorageSettings(): ConfigurationStorageSettings
    {
        if (!isset($this->configurationStorageSettings)) {
            $this->configurationStorageSettings = $this->globalConfiguration->getGlobalSettings(ConfigurationStorageSettings::class);
        }

        return $this->configurationStorageSettings;
    }

    protected function checkFileValidity(string $fileName): bool
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);

        return (bool)preg_match('/\.config$/', strtolower($baseName));
    }

    public function getIdentifiers(): array
    {
        $result = [];
        $folderIdentifiers = $this->registry->getStaticConfigurationDocumentFolderIdentifiers();
        foreach ($folderIdentifiers as $folderIdentifier) {
            $assetService = $this->registry->getResourceService($folderIdentifier);
            if (!$assetService instanceof ResourceServiceInterface) {
                continue;
            }

            $files = $assetService->getFilesInResourceFolder($folderIdentifier);
            if ($files === false) {
                continue;
            }

            foreach ($files as $file) {
                if (!$this->checkFileValidity($file)) {
                    continue;
                }

                $result[] = $assetService->getFileIdentifierInResourceFolder($folderIdentifier, $file);
            }
        }

        return $result;
    }

    public function match(string $identifier): bool
    {
        $assetService = $this->registry->getResourceService($identifier);
        if ($assetService instanceof ResourceServiceInterface) {
            return $assetService->resourceIdentifierMatch($identifier);
        }

        return false;
    }

    public function exists(string $identifier): bool
    {
        $assetService = $this->registry->getResourceService($identifier);
        if ($assetService instanceof ResourceServiceInterface) {
            return $assetService->resourceExists($identifier);
        }

        return false;
    }

    public function isReadonly(string $identifier): bool
    {
        return !$this->getConfigurationStorageSettings()->allowSaveToExtensionPaths();
    }

    public function getContent(string $identifier, bool $metaDataOnly = false): ?string
    {
        $assetService = $this->registry->getResourceService($identifier);
        if ($assetService instanceof ResourceServiceInterface) {
            return $assetService->getResourceContent($identifier);
        }

        return null;
    }

    public function setContent(string $identifier, string $content): void
    {
        $assetService = $this->registry->getResourceService($identifier);
        if ($assetService instanceof ResourceServiceInterface) {
            $assetService->setResourceContent($identifier, $content);
        }
    }
}

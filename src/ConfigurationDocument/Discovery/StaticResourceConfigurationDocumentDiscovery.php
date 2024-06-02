<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Discovery;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Resource\ResourceServiceInterface;

class StaticResourceConfigurationDocumentDiscovery implements StaticConfigurationDocumentDiscoveryInterface, GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
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

    public function readonly(string $identifier): bool
    {
        return !($this->globalConfiguration->get('core', [])['configurationStorage']['allowSaveToExtensionPaths'] ?? false);
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

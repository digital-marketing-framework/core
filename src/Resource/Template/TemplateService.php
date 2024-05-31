<?php

namespace DigitalMarketingFramework\Core\Resource\Template;

use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Resource\ResourceServiceInterface;

class TemplateService implements TemplateServiceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var array<string,int> */
    protected array $templateFolders = [];

    /** @var array<string,int> */
    protected array $partialFolders = [];

    protected string $errorMessageTemplateName = 'error.html.twig';

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    public function addTemplateFolder(string $identifier, int $priority = 100): void
    {
        $this->templateFolders[$identifier] = $priority;
    }

    public function addPartialFolder(string $identifier, int $priority = 100): void
    {
        $this->partialFolders[$identifier] = $priority;
    }

    public function getTemplateFolders(): array
    {
        $folders = $this->templateFolders;
        arsort($folders);

        return array_keys($folders);
    }

    public function getPartialFolders(): array
    {
        $folders = $this->partialFolders;
        arsort($folders);

        return array_keys($folders);
    }

    /**
     * @param array<string> $folders
     *
     * @return array<string>
     */
    protected function getTemplateOrPartialFolderPaths(array $folders): array
    {
        $result = [];
        foreach ($folders as $folderIdentifier) {
            $resourceService = $this->registry->getResourceService($folderIdentifier);

            if (!$resourceService instanceof ResourceServiceInterface) {
                $this->logger->warning(sprintf('No resource service found for identifier "%s".', $folderIdentifier));
                continue;
            }

            if (!$resourceService->resourceExists($folderIdentifier)) {
                $this->logger->warning(sprintf('Resource "%s" not found.', $folderIdentifier));
                continue;
            }

            $result[] = $resourceService->getResourcePath($folderIdentifier);
        }

        return $result;
    }

    public function getTemplateFolderPaths(): array
    {
        return $this->getTemplateOrPartialFolderPaths($this->getTemplateFolders());
    }

    public function getPartialFolderPaths(): array
    {
        return $this->getTemplateOrPartialFolderPaths($this->getPartialFolders());
    }

    /**
     * @param string|array<string> $possibleNames
     * @param array<string> $folders
     */
    protected function resolveTemplateOrPartialIdentifier(string|array $possibleNames, array $folders): ?string
    {
        if (is_string($possibleNames)) {
            $possibleNames = [$possibleNames];
        }

        foreach ($folders as $identifier) {
            $resourceService = $this->registry->getResourceService($identifier);

            if (!$resourceService instanceof ResourceServiceInterface) {
                continue;
            }

            foreach ($possibleNames as $possibleName) {
                $possibleIdentifier = $resourceService->getFileIdentifierInResourceFolder($identifier, $possibleName);
                if ($resourceService->resourceExists($possibleIdentifier)) {
                    return $possibleIdentifier;
                }
            }
        }

        return null;
    }

    public function resolveTemplateIdentifier(string|array $possibleNames): ?string
    {
        return $this->resolveTemplateOrPartialIdentifier($possibleNames, $this->getTemplateFolders());
    }

    public function resolvePartialIdentifier(string|array $possibleNames): ?string
    {
        return $this->resolveTemplateOrPartialIdentifier($possibleNames, $this->getPartialFolders());
    }

    /**
     * @param string|array<string> $possibleNames
     * @param array<string> $folders
     */
    protected function getTemplateOrPartial(string|array $possibleNames, array $folders): ?string
    {
        $identifier = $this->resolveTemplateOrPartialIdentifier($possibleNames, $folders);

        if ($identifier === null) {
            return null;
        }

        $resourceService = $this->registry->getResourceService($identifier);

        if (!$resourceService instanceof ResourceServiceInterface) {
            return null;
        }

        return $resourceService->getResourceContent($identifier);
    }

    public function getTemplate(string|array $possibleNames): ?string
    {
        return $this->getTemplateOrPartial($possibleNames, $this->getTemplateFolders());
    }

    public function getPartial(string|array $possibleNames): ?string
    {
        return $this->getTemplateOrPartial($possibleNames, $this->getPartialFolders());
    }

    public function setErrorMessageTemplateName(string $errorMessageTemplateName): void
    {
        $this->errorMessageTemplateName = $errorMessageTemplateName;
    }

    public function getErrorMessageTemplateName(): string
    {
        return $this->errorMessageTemplateName;
    }
}

<?php

namespace DigitalMarketingFramework\Core\FieldDefinition\Storage;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareInterface;
use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\ConfigurationStorageSettings;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Resource\ResourceServiceInterface;

/**
 * Field definition files across an ordered list of folders, lowest precedence first:
 * folders registered by packages, folders named in the global settings, and finally the one
 * writable folder.
 *
 * Folders are of two kinds and the storage resolves each to whatever reaches it: a package
 * folder is a resource identifier served by a ResourceService, the writable folder is a CMS
 * path served by the file storage. That difference is deliberately kept inside here, so
 * callers see one identifier space with one precedence order.
 */
class FieldDefinitionStorage implements FieldDefinitionStorageInterface, FileStorageAwareInterface, GlobalConfigurationAwareInterface
{
    use FileStorageAwareTrait;
    use GlobalConfigurationAwareTrait;

    public const FILE_SUFFIX = '.fields';

    public const FILE_EXTENSION = 'yaml';

    public const STORAGE_CONFIGURATION_KEY = CoreGlobalConfigurationSchema::KEY_FIELD_DEFINITION_STORAGE;

    protected ConfigurationStorageSettings $configurationStorageSettings;

    /**
     * File names per folder, listed once per request and dropped for a folder this storage
     * writes into.
     *
     * @var array<string,array<string>>
     */
    protected array $fileNames = [];

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    /**
     * Read raw rather than through GlobalSettings, matching the configuration document storage.
     * Both are constructed while services are being registered, and settings cannot be resolved
     * before the schema processors are — see GlobalConfigurationInterface.
     */
    protected function getStorageConfiguration(string $key, mixed $default = null): mixed
    {
        return $this->globalConfiguration->get('core')[static::STORAGE_CONFIGURATION_KEY][$key] ?? $default;
    }

    /**
     * Writing into package paths is a development-machine setting, shared with configuration
     * documents: it exists so definitions shipped by a package can be authored in the first
     * place, not so a live system can edit them.
     */
    protected function allowSaveToExtensionPaths(): bool
    {
        if (!isset($this->configurationStorageSettings)) {
            $this->configurationStorageSettings = $this->globalConfiguration->getGlobalSettings(ConfigurationStorageSettings::class);
        }

        return $this->configurationStorageSettings->allowSaveToExtensionPaths();
    }

    public function getStorageFolder(): string
    {
        return rtrim((string)$this->getStorageConfiguration(CoreGlobalConfigurationSchema::KEY_FIELD_DEFINITION_STORAGE_FOLDER, ''), '/');
    }

    public function protectStorageFolder(): void
    {
        $folder = $this->getStorageFolder();
        if ($folder !== '') {
            $this->fileStorage->protectFolder($folder);
        }
    }

    /**
     * Every searched folder, lowest precedence first.
     *
     * @return array<string>
     */
    protected function getAllFolders(): array
    {
        // Package folders and the folders named in the global settings are both registered
        // during initialisation, in that order. Only the writable folder is added here,
        // last, so it wins over everything shipped.
        $folders = $this->registry->getFieldDefinitionFolderIdentifiers();

        $writableFolder = $this->getStorageFolder();
        if ($writableFolder !== '' && !in_array($writableFolder, $folders, true)) {
            $folders[] = $writableFolder;
        }

        return $folders;
    }

    protected function getResourceService(string $identifier): ?ResourceServiceInterface
    {
        return $this->registry->getResourceService($identifier);
    }

    protected function buildFileIdentifier(string $folder, string $contextIdentifier): string
    {
        return rtrim($folder, '/') . '/' . $this->buildFileName($contextIdentifier);
    }

    protected function buildFileName(string $contextIdentifier): string
    {
        return $contextIdentifier . static::FILE_SUFFIX . '.' . static::FILE_EXTENSION;
    }

    /**
     * Recovers the context identifier a file name stands for, or null when the file is not
     * a field definition file at all.
     *
     * Case and extension both count, because the identifier is turned back into a file name
     * by buildFileName(): accepting a name it would not produce yields a row in the list that
     * points at a file which is not there.
     */
    protected function extractContextIdentifier(string $fileName): ?string
    {
        $suffix = static::FILE_SUFFIX . '.' . static::FILE_EXTENSION;
        if (!str_ends_with($fileName, $suffix)) {
            return null;
        }

        $contextIdentifier = substr($fileName, 0, -strlen($suffix));

        return $contextIdentifier === '' ? null : $contextIdentifier;
    }

    /**
     * @return array<string> file names, not full identifiers
     */
    protected function getFileNamesInFolder(string $folder): array
    {
        $resourceService = $this->getResourceService($folder);
        if ($resourceService instanceof ResourceServiceInterface) {
            $files = $resourceService->getFilesInResourceFolder($folder);

            return $files === false ? [] : $files;
        }

        if (!$this->fileStorage->folderExists($folder)) {
            return [];
        }

        $names = [];
        foreach ($this->fileStorage->getFilesFromFolder($folder) as $fileIdentifier) {
            $name = $this->fileStorage->getFileName($fileIdentifier);
            if ($name !== null) {
                $names[] = $name;
            }
        }

        return $names;
    }

    /**
     * The file names in a folder, listed at most once per request.
     *
     * Listing a folder and asking about one file in it cost about the same, and the list view
     * asks about every context across every registered folder — of which an installation has
     * dozens. So every existence check here is answered from the listing.
     *
     * @return array<string>
     */
    protected function getFileNames(string $folder): array
    {
        $this->fileNames[$folder] ??= $this->getFileNamesInFolder($folder);

        return $this->fileNames[$folder];
    }

    protected function folderHasContext(string $folder, string $contextIdentifier): bool
    {
        return in_array($this->buildFileName($contextIdentifier), $this->getFileNames($folder), true);
    }

    /**
     * The file for this context in the given folder, or in the highest-precedence folder
     * that has one when no folder is given.
     */
    protected function findFileIdentifier(string $contextIdentifier, ?string $folder = null): ?string
    {
        if ($folder !== null) {
            return $this->folderHasContext($folder, $contextIdentifier)
                ? $this->buildFileIdentifier($folder, $contextIdentifier)
                : null;
        }

        $found = null;
        foreach ($this->getAllFolders() as $searchFolder) {
            if ($this->folderHasContext($searchFolder, $contextIdentifier)) {
                $found = $this->buildFileIdentifier($searchFolder, $contextIdentifier);
            }
        }

        return $found;
    }

    public function getFolders(string $contextIdentifier): array
    {
        $folders = [];
        foreach ($this->getAllFolders() as $folder) {
            if ($this->folderHasContext($folder, $contextIdentifier)) {
                $folders[] = $folder;
            }
        }

        return $folders;
    }

    public function getWritableFolders(): array
    {
        $folders = [];
        foreach ($this->getAllFolders() as $folder) {
            if (!$this->isFolderReadOnly($folder)) {
                $folders[] = $folder;
            }
        }

        return $folders;
    }

    protected function isFolderReadOnly(string $folder): bool
    {
        if ($folder === $this->getStorageFolder()) {
            return false;
        }

        return !$this->allowSaveToExtensionPaths();
    }

    public function getContextIdentifiers(): array
    {
        $identifiers = [];
        foreach ($this->getAllFolders() as $folder) {
            foreach ($this->getFileNames($folder) as $fileName) {
                $contextIdentifier = $this->extractContextIdentifier($fileName);
                if ($contextIdentifier !== null) {
                    $identifiers[$contextIdentifier] = true;
                }
            }
        }

        return array_keys($identifiers);
    }

    public function exists(string $contextIdentifier, ?string $folder = null): bool
    {
        return $this->findFileIdentifier($contextIdentifier, $folder) !== null;
    }

    public function getContent(string $contextIdentifier, ?string $folder = null): ?string
    {
        $fileIdentifier = $this->findFileIdentifier($contextIdentifier, $folder);
        if ($fileIdentifier === null) {
            return null;
        }

        $resourceService = $this->getResourceService($fileIdentifier);
        if ($resourceService instanceof ResourceServiceInterface) {
            return $resourceService->getResourceContent($fileIdentifier);
        }

        return $this->fileStorage->getFileContents($fileIdentifier);
    }

    public function isStorageReady(): bool
    {
        $folder = $this->getStorageFolder();

        return $folder !== '' && $this->fileStorage->folderIsWriteable($folder);
    }

    public function isStoragePubliclyAccessible(): bool
    {
        $folder = $this->getStorageFolder();

        return $folder !== '' && $this->fileStorage->isPubliclyAccessible($folder);
    }

    public function isStorageProtected(): bool
    {
        $folder = $this->getStorageFolder();

        return $folder === '' || $this->fileStorage->folderIsProtected($folder);
    }

    public function setContent(string $contextIdentifier, string $content, ?string $folder = null): void
    {
        // Never derived from where the context already exists: a package shipping a context
        // must not become the write target just because it happens to hold the only file.
        // Which layer to write is the caller's decision.
        $folder ??= $this->getStorageFolder();
        if ($folder === '' || $this->isFolderReadOnly($folder)) {
            throw new DigitalMarketingFrameworkException(sprintf('Field definition folder "%s" cannot be written to.', $folder));
        }

        unset($this->fileNames[$folder]);
        $fileIdentifier = $this->buildFileIdentifier($folder, $contextIdentifier);

        $resourceService = $this->getResourceService($fileIdentifier);
        if ($resourceService instanceof ResourceServiceInterface) {
            $resourceService->setResourceContent($fileIdentifier, $content);

            return;
        }

        $this->fileStorage->putFileContents($fileIdentifier, $content);
    }

    public function delete(string $contextIdentifier, ?string $folder = null): void
    {
        $folder ??= $this->getStorageFolder();
        $fileIdentifier = $this->findFileIdentifier($contextIdentifier, $folder);
        if ($fileIdentifier === null || $this->isFolderReadOnly($folder)) {
            return;
        }

        unset($this->fileNames[$folder]);

        $resourceService = $this->getResourceService($fileIdentifier);
        if ($resourceService instanceof ResourceServiceInterface) {
            $resourceService->deleteResource($fileIdentifier);

            return;
        }

        $this->fileStorage->deleteFile($fileIdentifier);
    }

    public function isReadOnly(string $contextIdentifier, ?string $folder = null): bool
    {
        if ($folder !== null) {
            return $this->isFolderReadOnly($folder);
        }

        // Without a folder the question is whether this context can be edited at all, which
        // it can as long as some layer is writable — a new file in a writable folder takes
        // precedence over whatever a package ships.
        return $this->getWritableFolders() === [];
    }
}

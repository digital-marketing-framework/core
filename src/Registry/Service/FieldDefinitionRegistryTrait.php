<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\FieldDefinition\FieldDefinitionManager;
use DigitalMarketingFramework\Core\FieldDefinition\FieldDefinitionManagerInterface;
use DigitalMarketingFramework\Core\FieldDefinition\Storage\FieldDefinitionStorage;
use DigitalMarketingFramework\Core\FieldDefinition\Storage\FieldDefinitionStorageInterface;

trait FieldDefinitionRegistryTrait
{
    /** @var array<string> */
    protected array $fieldDefinitionFolderIdentifiers = [];

    protected FieldDefinitionStorageInterface $fieldDefinitionStorage;

    protected FieldDefinitionManagerInterface $fieldDefinitionManager;

    public function addFieldDefinitionFolderIdentifier(string $identifier): void
    {
        $identifier = rtrim($identifier, '/');
        if (!in_array($identifier, $this->fieldDefinitionFolderIdentifiers, true)) {
            $this->fieldDefinitionFolderIdentifiers[] = $identifier;
        }
    }

    public function getFieldDefinitionFolderIdentifiers(): array
    {
        return $this->fieldDefinitionFolderIdentifiers;
    }

    public function getFieldDefinitionStorage(): FieldDefinitionStorageInterface
    {
        if (!isset($this->fieldDefinitionStorage)) {
            $this->setFieldDefinitionStorage($this->createObject(FieldDefinitionStorage::class, [$this]));
        }

        return $this->fieldDefinitionStorage;
    }

    public function setFieldDefinitionStorage(FieldDefinitionStorageInterface $fieldDefinitionStorage): void
    {
        $this->fieldDefinitionStorage = $fieldDefinitionStorage;
    }

    public function getFieldDefinitionManager(): FieldDefinitionManagerInterface
    {
        $this->fieldDefinitionManager ??= $this->createObject(FieldDefinitionManager::class, [$this->getFieldDefinitionStorage()]);

        return $this->fieldDefinitionManager;
    }

    public function setFieldDefinitionManager(FieldDefinitionManagerInterface $fieldDefinitionManager): void
    {
        $this->fieldDefinitionManager = $fieldDefinitionManager;
    }
}

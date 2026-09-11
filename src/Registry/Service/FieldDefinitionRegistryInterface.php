<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\FieldDefinition\FieldDefinitionManagerInterface;
use DigitalMarketingFramework\Core\FieldDefinition\Storage\FieldDefinitionStorageInterface;

interface FieldDefinitionRegistryInterface
{
    /**
     * Registers a folder that a package ships field definition files in. Folders are
     * searched in registration order, each one taking precedence over those before it.
     */
    public function addFieldDefinitionFolderIdentifier(string $identifier): void;

    /**
     * @return array<string> lowest precedence first
     */
    public function getFieldDefinitionFolderIdentifiers(): array;

    public function getFieldDefinitionStorage(): FieldDefinitionStorageInterface;

    public function setFieldDefinitionStorage(FieldDefinitionStorageInterface $fieldDefinitionStorage): void;

    public function getFieldDefinitionManager(): FieldDefinitionManagerInterface;

    public function setFieldDefinitionManager(FieldDefinitionManagerInterface $fieldDefinitionManager): void;
}

<?php

namespace DigitalMarketingFramework\Core\Model\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

/**
 * Migratable for storage-backed configuration documents (central configs).
 *
 * Reads and writes documents through the ConfigurationDocumentManager.
 */
class StorageMigratable extends Migratable
{
    public const SOURCE = 'storage';

    public function __construct(
        protected string $identifier,
        protected string $name,
        protected bool $readOnly,
        protected ConfigurationDocumentManagerInterface $manager,
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    public function getSource(): string
    {
        return self::SOURCE;
    }

    public function getMigratableGroup(): string
    {
        return 'storage';
    }

    public function getConfigurationDocument(): string
    {
        return $this->manager->getDocumentFromIdentifier($this->identifier);
    }

    public function saveConfigurationDocument(string $document, SchemaDocument $schemaDocument): void
    {
        $this->manager->saveDocument($this->identifier, $document, $schemaDocument);
    }
}

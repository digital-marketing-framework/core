<?php

namespace DigitalMarketingFramework\Core\Model\ConfigurationDocument;

use DigitalMarketingFramework\Core\DataSource\DataSourceManagerInterface;
use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

/**
 * Migratable for data source embedded configuration documents.
 *
 * Reads documents from the data source, writes through the data source manager.
 * Examples: API endpoint configs, form embedded configs.
 */
class DataSourceMigratable extends Migratable
{
    public const SOURCE = 'dataSource';

    /**
     * @param DataSourceManagerInterface<DataSourceInterface> $dataSourceManager
     */
    public function __construct(
        protected DataSourceInterface $dataSource,
        protected DataSourceManagerInterface $dataSourceManager,
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->dataSource->getIdentifier();
    }

    public function getName(): string
    {
        return $this->dataSource->getName();
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function getSource(): string
    {
        return self::SOURCE;
    }

    public function getConfigurationDocument(): string
    {
        return $this->dataSource->getConfigurationDocument();
    }

    public function saveConfigurationDocument(string $document, SchemaDocument $schemaDocument): void
    {
        $this->dataSourceManager->updateConfigurationDocument($this->dataSource, $document);
    }

    // -- MigratableInterface (delegated to data source) --

    public function getMigratableGroup(): string
    {
        return $this->dataSource->getType();
    }

    public function getDescription(): string
    {
        return $this->dataSource->getDescription();
    }

    public function canHaveVariants(): bool
    {
        return $this->dataSource->canHaveVariants();
    }

    public function getBaseMigratableIdentifier(): ?string
    {
        return $this->dataSource->getBaseDataSourceIdentifier();
    }
}

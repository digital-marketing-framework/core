<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;

/**
 * @template DataSourceClass of DataSourceInterface
 */
interface DataSourceStorageInterface extends PluginInterface
{
    public function matches(string $id): bool;

    public function getType(): string;

    /**
     * @return ?DataSourceClass
     */
    public function getDataSourceByIdentifier(string $identifier): ?DataSourceInterface;

    /**
     * @return array<DataSourceClass>
     */
    public function getAllDataSources(): array;

    /**
     * Returns all data source variants, including inactive or disabled ones.
     * Used by the migration system to discover all configuration documents.
     *
     * Default implementation falls back to getAllDataSources().
     *
     * @return array<DataSourceClass>
     */
    public function getAllDataSourceVariants(): array;

    /**
     * Returns the identifiers of all data source variants, without loading full objects.
     * Used for lightweight counting and pagination.
     *
     * Default implementation delegates to getAllDataSourceVariants().
     *
     * @return array<string>
     */
    public function getAllDataSourceVariantIdentifiers(): array;

    /**
     * Returns a single data source variant by its full identifier.
     * Used for single-document lookup without loading all variants.
     *
     * @param bool $maintenanceMode When true, the data source returns raw stored data
     *        (e.g. override documents regardless of activation flags).
     *        When false (default), the data source respects runtime flags.
     *
     * @return ?DataSourceClass
     */
    public function getDataSourceVariantByIdentifier(string $identifier, bool $maintenanceMode = false): ?DataSourceInterface;

    /**
     * Updates the configuration document for a data source in its storage backend.
     * Used by the migration system to persist migrated configuration documents.
     */
    public function updateConfigurationDocument(DataSourceInterface $dataSource, string $document): void;
}

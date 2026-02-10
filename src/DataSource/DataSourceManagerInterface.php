<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;

/**
 * @template DataSourceClass of DataSourceInterface
 */
interface DataSourceManagerInterface
{
    /**
     * @param array<string,mixed> $dataSourceContext
     *
     * @return ?DataSourceClass
     */
    public function getDataSourceById(string $id, array $dataSourceContext): ?DataSourceInterface;

    /**
     * @return array<DataSourceClass>
     */
    public function getAllDataSources(): array;

    /**
     * Returns all data source variants from all storages, including inactive or disabled ones.
     * Used by the migration system to discover all configuration documents.
     *
     * @return array<DataSourceClass>
     */
    public function getAllDataSourceVariants(): array;

    /**
     * Returns identifiers of all data source variants from all storages,
     * without loading full data source objects.
     *
     * @return array<string>
     */
    public function getAllDataSourceVariantIdentifiers(): array;

    /**
     * Returns a single data source variant by its full identifier.
     * Routes to the matching storage based on the identifier prefix.
     *
     * @return ?DataSourceClass
     */
    public function getDataSourceVariantByIdentifier(string $identifier): ?DataSourceInterface;

    /**
     * Updates the configuration document for a data source.
     * Delegates to the matching storage based on the data source identifier.
     */
    public function updateConfigurationDocument(DataSourceInterface $dataSource, string $document): void;
}

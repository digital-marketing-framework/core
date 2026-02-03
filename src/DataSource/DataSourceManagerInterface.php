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
}

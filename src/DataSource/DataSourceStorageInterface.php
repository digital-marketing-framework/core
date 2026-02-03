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
     * Returns all data source variants, including inactive or disabled ones.
     * Used by the migration system to discover all configuration documents.
     *
     * Default implementation falls back to getAllDataSources().
     *
     * @return array<DataSourceClass>
     */
    public function getAllDataSourceVariants(): array;
}

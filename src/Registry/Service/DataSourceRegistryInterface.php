<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\DataSource\CoreDataSourceStorageInterface;
use DigitalMarketingFramework\Core\DataSource\DataSourceManagerInterface;
use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;

interface DataSourceRegistryInterface
{
    /**
     * @return DataSourceManagerInterface<DataSourceInterface>
     */
    public function getCoreDataSourceManager(): DataSourceManagerInterface;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerCoreSourceStorage(string $class, array $additionalArguments = [], string $keyword = ''): void;

    /**
     * @return array<CoreDataSourceStorageInterface<DataSourceInterface>>
     */
    public function getAllCoreSourceStorages(): array;

    /**
     * Register a DataSourceManager for use by the ConfigurationDocumentMaintenanceService.
     *
     * @template T of DataSourceInterface
     *
     * @param DataSourceManagerInterface<T> $dataSourceManager
     */
    public function addDataSourceManager(DataSourceManagerInterface $dataSourceManager): void;

    /**
     * @return array<DataSourceManagerInterface<DataSourceInterface>>
     */
    public function getDataSourceManagers(): array;
}

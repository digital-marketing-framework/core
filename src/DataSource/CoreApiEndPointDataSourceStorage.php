<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Model\DataSource\ApiEndPointDataSource;
use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;

/**
 * @extends AbstractApiEndPointDataSourceStorage<ApiEndPointDataSource>
 *
 * @implements CoreDataSourceStorageInterface<ApiEndPointDataSource>
 */
class CoreApiEndPointDataSourceStorage extends AbstractApiEndPointDataSourceStorage implements CoreDataSourceStorageInterface
{
    protected function createDataSource(EndPointInterface $endPoint): DataSourceInterface
    {
        return new ApiEndPointDataSource($endPoint);
    }

    /**
     * Filter for getAllDataSources() and getDataSourceById().
     * Core: only checks the general enabled flag.
     */
    protected function filterEndPoint(EndPointInterface $endPoint): bool
    {
        return $endPoint->getEnabled();
    }

    /**
     * Filter for getAllDataSourceVariants().
     * Core: no filter — all endpoints are variants for migration discovery.
     */
    protected function filterEndPointVariant(EndPointInterface $endPoint): bool
    {
        return true;
    }
}

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
}

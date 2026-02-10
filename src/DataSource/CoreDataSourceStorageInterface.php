<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;

/**
 * @template DataSourceClass of DataSourceInterface
 *
 * @extends DataSourceStorageInterface<DataSourceClass>
 */
interface CoreDataSourceStorageInterface extends DataSourceStorageInterface
{
}

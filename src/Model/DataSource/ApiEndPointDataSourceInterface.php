<?php

namespace DigitalMarketingFramework\Core\Model\DataSource;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

interface ApiEndPointDataSourceInterface extends DataSourceInterface
{
    public function getEndPoint(): EndPointInterface;
}

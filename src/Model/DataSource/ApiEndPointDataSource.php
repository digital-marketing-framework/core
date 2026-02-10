<?php

namespace DigitalMarketingFramework\Core\Model\DataSource;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

class ApiEndPointDataSource extends DataSource
{
    public const TYPE = 'api';

    public function __construct(
        protected EndPointInterface $endPoint,
    ) {
        $hashData = [
            'name' => $this->endPoint->getName(),
            'enabled' => $this->endPoint->getEnabled(),
            'pushEnabled' => $this->endPoint->getPushEnabled(),
            'pullEnabled' => $this->endPoint->getPullEnabled(),
            'disableContext' => $this->endPoint->getDisableContext(),
            'allowContextOverride' => $this->endPoint->getAllowContextOverride(),
            'exposeToFrontend' => $this->endPoint->getExposeToFrontend(),
            'configurationDocument' => $this->endPoint->getConfigurationDocument(),
        ];
        $hash = md5(json_encode($hashData));

        parent::__construct(
            'core',
            static::TYPE,
            static::TYPE . ':' . $endPoint->getName(),
            $endPoint->getName(),
            $hash,
            $endPoint->getConfigurationDocument()
        );
    }

    public function getEndPoint(): EndPointInterface
    {
        return $this->endPoint;
    }
}

<?php

namespace DigitalMarketingFramework\Core\Integration;

class SystemIntegrationInfo extends IntegrationInfo
{
    public function __construct()
    {
        parent::__construct(
            'system',
            weight: static::WEIGHT_BOTTOM,
            inboundRouteListLabel: 'Inbound System Routes',
            outboundRouteListLabel: 'Outbound System Routes'
        );
    }
}

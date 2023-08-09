<?php

namespace DigitalMarketingFramework\Core\Registry;

interface RegistryDomain
{
    /**
     * The "core" domain encompasses everything that is shared by the distributor and the collector.
     */
    const CORE = 'core';

    /**
     * The "distributor" domain encompasses everything that is needed by the distributor but not by the collector.
     */
    const DISTRIBUTOR = 'distributor';

    /**
     * The "collector" domain encompasses everything that is needed by the collector but not by the distributor.
     */
    const COLLECTOR = 'collector';
}

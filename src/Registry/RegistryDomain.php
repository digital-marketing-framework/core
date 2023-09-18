<?php

namespace DigitalMarketingFramework\Core\Registry;

interface RegistryDomain
{
    /**
     * The "core" domain encompasses everything that is shared by the distributor and the collector.
     */
    public const CORE = 'core';

    /**
     * The "distributor" domain encompasses everything that is needed by the distributor but not by the collector.
     */
    public const DISTRIBUTOR = 'distributor';

    /**
     * The "collector" domain encompasses everything that is needed by the collector but not by the distributor.
     */
    public const COLLECTOR = 'collector';
}

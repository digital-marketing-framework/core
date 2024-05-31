<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Resource\VendorResourceServiceInterface;

interface VendorResourceServiceRegistryInterface
{
    public function getVendorResourceService(): VendorResourceServiceInterface;

    public function setVendorResourceService(VendorResourceServiceInterface $vendorAssetService): void;
}

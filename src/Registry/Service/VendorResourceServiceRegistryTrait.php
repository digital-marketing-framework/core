<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Resource\VendorResourceService;
use DigitalMarketingFramework\Core\Resource\VendorResourceServiceInterface;

trait VendorResourceServiceRegistryTrait
{
    protected VendorResourceServiceInterface $vendorResourceService;

    public function getVendorResourceService(): VendorResourceServiceInterface
    {
        if (!isset($this->vendorResourceService)) {
            $this->vendorResourceService = $this->createObject(VendorResourceService::class);
        }

        return $this->vendorResourceService;
    }

    public function setVendorResourceService(VendorResourceServiceInterface $vendorAssetService): void
    {
        $this->vendorResourceService = $vendorAssetService;
    }
}

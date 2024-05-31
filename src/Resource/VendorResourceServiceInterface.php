<?php

namespace DigitalMarketingFramework\Core\Resource;

interface VendorResourceServiceInterface extends ResourceServiceInterface
{
    public function getVendorPath(): string;

    public function setVendorPath(string $vendorPath): void;
}

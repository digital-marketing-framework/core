<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Request\RequestInterface;

interface RequestRegistryInterface
{
    public function getRequest(): RequestInterface;
    public function setRequest(RequestInterface $request): void;
}

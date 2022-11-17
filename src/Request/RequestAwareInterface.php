<?php

namespace DigitalMarketingFramework\Core\Request;

interface RequestAwareInterface
{
    public function setRequest(RequestInterface $request): void;
}

<?php

namespace DigitalMarketingFramework\Core\Request;

trait RequestAwareTrait
{
    protected RequestInterface $request;

    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }
}

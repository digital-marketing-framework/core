<?php

namespace DigitalMarketingFramework\Core\Backend\Response;

class RedirectResponse extends Response
{
    public function __construct(
        protected string $redirectLocation,
        int $statusCode = 301,
        array $headers = [],
    ) {
        $headers['Location'] = $redirectLocation;
        parent::__construct('', $statusCode, $headers);
    }

    public function getRedirectLocation(): string
    {
        return $this->redirectLocation;
    }

    public function setRedirectLocation(string $redirectLocation): void
    {
        $this->redirectLocation = $redirectLocation;
        $this->setHeader('Location', $redirectLocation);
    }
}

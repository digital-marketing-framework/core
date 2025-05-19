<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Core\Backend\Controller\BackendController;
use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class AjaxController extends BackendController implements AjaxControllerInterface
{
    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        string $section,
        array $routes,
    ) {
        parent::__construct($keyword, $registry, 'ajax', $section, $routes);
    }

    public function getResponse(Request $request): Response
    {
        $this->request = $request;

        return $this->callActionMethod();
    }
}

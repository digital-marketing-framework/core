<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class DashboardSectionController extends SectionController
{
    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'core',
            ['index']
        );
    }

    protected function indexAction(): Response
    {
        $this->viewData['sections'] = $this->registry->getBackendManager()->getAllSections();
        $this->viewData['alerts'] = $this->registry->getBackendManager()->getAlerts();

        return $this->render();
    }
}

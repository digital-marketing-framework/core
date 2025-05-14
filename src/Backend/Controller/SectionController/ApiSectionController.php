<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ApiSectionController extends SectionController
{
    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'api',
            ['list', 'edit', 'save', 'create', 'delete']
        );
    }

    protected function listAction(Request $request): Response
    {
        // $this->viewData['sections'] = $this->registry->getBackendManager()->getAllSections();

        return $this->render($request);
    }

    protected function editAction(Request $request): Response
    {
        return $this->render($request);
    }

    protected function saveAction(Request $request): Response
    {
        // TODO save

        return $this->redirect('page.api.list');
    }

    protected function createAction(Request $request): Response
    {
        // TODO create
        $id = 42;

        return $this->redirect('page.api.edit', [
            'id' => $id,
        ]);
    }

    protected function deleteAction(Request $request): Response
    {
        // TODO delete

        return $this->redirect('page.api.list');
    }
}

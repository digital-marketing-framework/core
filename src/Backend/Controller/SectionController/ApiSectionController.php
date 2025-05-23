<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

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

    protected function listAction(): Response
    {
        return $this->render();
    }

    protected function editAction(): Response
    {
        return $this->render();
    }

    protected function saveAction(): Response
    {
        // TODO save

        return $this->redirect('page.api.list');
    }

    protected function createAction(): Response
    {
        // TODO create
        $id = 42;

        return $this->redirect('page.api.edit', [
            'id' => $id,
        ]);
    }

    protected function deleteAction(): Response
    {
        // TODO delete

        return $this->redirect('page.api.list');
    }
}

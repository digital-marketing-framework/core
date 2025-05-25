<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use BadMethodCallException;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareInterface;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareTrait;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ApiSectionController extends ListSectionController implements EndPointStorageAwareInterface
{
    use EndPointStorageAwareTrait;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'api',
            ['list', 'edit', 'save', 'create', 'delete']
        );
    }

    protected function fetchFilteredCount(array $filters): int
    {
        return $this->endPointStorage->getEndPointCount();
    }

    protected function fetchFiltered(array $filters, array $navigation): array
    {
        return $this->endPointStorage->getEndPointsFiltered($navigation);
    }

    protected function listAction(): Response
    {
        $this->setUpListView();

        return $this->render();
    }

    protected function editAction(): Response
    {
        throw new BadMethodCallException('API endpoint edit action not implemented in core package');
    }

    protected function saveAction(): Response
    {
        throw new BadMethodCallException('API endpoint save action not implemented in core package');
    }

    protected function createAction(): Response
    {
        $name = $this->getParameters()['name'] ?? '';
        $endPoint = $this->endPointStorage->createEndPoint($name);
        $this->endPointStorage->addEndPoint($endPoint);

        return $this->redirect('page.api.edit', [
            'id' => $endPoint->getId(),
            'returnUrl' => $this->getReturnUrl(),
        ]);
    }

    protected function deleteAction(): Response
    {
        $ids = $this->getSelectedItems();
        $endPoints = $this->endPointStorage->fetchByIdList($ids);
        foreach ($endPoints as $endPoint) {
            $this->endPointStorage->removeEndPoint($endPoint);
        }

        return $this->redirect('page.api.list');
    }
}

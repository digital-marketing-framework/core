<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareInterface;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareTrait;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

/**
 * @extends ListSectionController<EndPointInterface>
 */
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

    protected function getItemStorage()
    {
        return $this->endPointStorage;
    }

    protected function createAction(): Response
    {
        $name = $this->getParameters()['name'] ?? '';
        $endPoint = $this->endPointStorage->create(['name' => $name]);
        $this->endPointStorage->add($endPoint);

        return $this->redirect('page.api.edit', [
            'id' => $endPoint->getId(),
            'returnUrl' => $this->getReturnUrl(),
        ]);
    }
}

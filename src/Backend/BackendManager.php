<?php

namespace DigitalMarketingFramework\Core\Backend;

use DigitalMarketingFramework\Core\Backend\Controller\AjaxController\AjaxControllerInterface;
use DigitalMarketingFramework\Core\Backend\Controller\BackendControllerInterface;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionControllerInterface;
use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\HtmlResponse;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Backend\Section\CoreIndexSection;
use DigitalMarketingFramework\Core\Backend\Section\SectionInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Alert\Alert;
use DigitalMarketingFramework\Core\Model\Alert\AlertInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class BackendManager implements BackendManagerInterface
{
    /** @var array<SectionInterface> */
    protected array $sections = [];

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    protected function getSectionController(Request $request): ?SectionControllerInterface
    {
        foreach ($this->registry->getAllBackendSectionControllers() as $controller) {
            if ($controller->matchRequest($request)) {
                return $controller;
            }
        }

        return null;
    }

    protected function getAjaxController(Request $request): ?AjaxControllerInterface
    {
        foreach ($this->registry->getAllBackendAjaxControllers() as $controller) {
            if ($controller->matchRequest($request)) {
                return $controller;
            }
        }

        return null;
    }

    public function getResponse(Request $request): Response
    {
        try {
            $controller = match ($request->getType()) {
                'page' => $this->getSectionController($request),
                'ajax' => $this->getAjaxController($request),
                default => throw new DigitalMarketingFrameworkException(sprintf('Unknown request type "%s"', $request->getType()))
            };

            if (!$controller instanceof BackendControllerInterface) {
                throw new DigitalMarketingFrameworkException(sprintf('Unknown route "%s"', $request->getRoute()));
            }

            return $controller->getResponse($request);
        } catch (DigitalMarketingFrameworkException $e) {
            return new HtmlResponse('An error occurred: ' . $e->getMessage());
        }
    }

    public function setSection(SectionInterface $section): void
    {
        $this->sections[$section->getName()] = $section;
    }

    public function getAllSections(): array
    {
        return $this->sections;
    }

    public function getSection(string $name): ?SectionInterface
    {
        if ($name === 'core') {
            return new CoreIndexSection();
        }

        return $this->sections[$name] ?? null;
    }

    public function getSectionMenu(Request $request): array
    {
        $menu = [];
        $sections = [new CoreIndexSection(), ...$this->sections];
        foreach ($sections as $section) {
            $menu[] = [
                'route' => $section->getRoute(),
                'label' => $section->getTitle(),
                'active' => $request->getSection() === $section->getName(),
            ];
        }

        return $menu;
    }

    public function getAlerts(): array
    {
        return $this->registry->getAlertManager()->getAllAlerts();
    }
}

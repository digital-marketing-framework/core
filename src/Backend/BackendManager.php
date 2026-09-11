<?php

namespace DigitalMarketingFramework\Core\Backend;

use DigitalMarketingFramework\Core\Backend\Controller\AjaxController\AjaxControllerInterface;
use DigitalMarketingFramework\Core\Backend\Controller\BackendControllerInterface;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionController;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionControllerInterface;
use DigitalMarketingFramework\Core\Backend\Response\HtmlResponse;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Backend\Section\CoreIndexSection;
use DigitalMarketingFramework\Core\Backend\Section\SectionInterface;
use DigitalMarketingFramework\Core\Backend\Section\SubSectionInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use Throwable;

class BackendManager implements BackendManagerInterface
{
    /** @var array<SectionInterface> */
    protected array $sections = [];

    /** @var array<string,array<string,SubSectionInterface>> */
    protected array $subSections = [];

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
                default => throw new DigitalMarketingFrameworkException(sprintf('Unknown request type "%s"', $request->getType())),
            };

            if (!$controller instanceof BackendControllerInterface) {
                throw new DigitalMarketingFrameworkException(sprintf('Unknown route "%s"', $request->getRoute()));
            }

            return $controller->getResponse($request);
        } catch (DigitalMarketingFrameworkException $e) {
            return $this->getErrorResponse($request, $e->getMessage());
        }
    }

    /**
     * An error rendered as a backend page, so that a refused action leaves the reader somewhere
     * they can act rather than on a bare sentence.
     *
     * Only for an action that cannot be carried out at all. Saying anything else — that one
     * succeeded, or refusing without leaving the page — needs a flash message system, which
     * does not exist yet.
     */
    protected function getErrorResponse(Request $request, string $message): Response
    {
        try {
            $section = $this->getSection($request->getSection()) ?? new CoreIndexSection();

            $content = $this->registry->getTemplateEngine()->render(
                ['templateName' => 'module-error.html.twig'],
                [
                    'error' => $message,
                    'section' => $section,
                    'menu' => $this->getSectionMenu($request),
                    'returnUri' => $this->registry->getBackendUriBuilder()->build($section->getRoute()),
                    'styles' => ['backend' => SectionController::BACKEND_STYLES],
                    'scripts' => ['menu' => SectionController::MENU_SCRIPT],
                ],
                false
            );

            $response = new HtmlResponse($content);
            $response->setStyleSheet('backend', SectionController::BACKEND_STYLES);
            $response->setScript('menu', SectionController::MENU_SCRIPT);

            return $response;
        } catch (Throwable) {
            // Whatever went wrong may be the very thing needed to render a page. The message
            // that was asked for still gets through, plainly, rather than being replaced by a
            // second failure about templates.
            return new HtmlResponse('An error occurred: ' . $message);
        }
    }

    protected function sortSections(): void
    {
        uasort($this->sections, static fn (SectionInterface $a, SectionInterface $b) => $a->getWeight() <=> $b->getWeight());
    }

    public function setSection(SectionInterface $section): void
    {
        $this->sections[$section->getName()] = $section;
        $this->sortSections();
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

    /**
     * Accumulates rather than replaces, unlike setSection(): several packages contributing
     * actions to one section is the case this exists for.
     */
    public function addSubSection(SubSectionInterface $subSection): void
    {
        $section = $subSection->getSection();
        $this->subSections[$section][$subSection->getName()] = $subSection;
        uasort(
            $this->subSections[$section],
            static fn (SubSectionInterface $a, SubSectionInterface $b): int => $a->getWeight() <=> $b->getWeight()
        );
    }

    /**
     * The subsections of the section a request belongs to, marked with which one is in use.
     *
     * A section with fewer than two of them gets an empty menu: there is nothing to choose
     * between, and a lone button pointing at the page you are already on is noise. This is why
     * a section that registers none never has to know the concept exists.
     */
    public function getSubSectionMenu(Request $request): array
    {
        $subSections = $this->subSections[$request->getSection()] ?? [];
        if (count($subSections) < 2) {
            return [];
        }

        $menu = [];
        foreach ($subSections as $subSection) {
            $menu[] = [
                'route' => $subSection->getRoute(),
                'label' => $subSection->getTitle(),
                'icon' => $subSection->getIcon(),
                'active' => $subSection->matchesAction($request->getInternalRoute()),
            ];
        }

        return $menu;
    }

    public function getSectionMenu(Request $request): array
    {
        $ungrouped = [];
        $groups = [];

        $sections = [new CoreIndexSection(), ...$this->sections];
        foreach ($sections as $section) {
            $entry = [
                'route' => $section->getRoute(),
                'label' => $section->getTitle(),
                'active' => $request->getSection() === $section->getName(),
            ];

            $subTitle = $section->getSubTitle();
            if ($subTitle === '') {
                $ungrouped[] = $entry;
            } else {
                $groups[$subTitle][] = $entry;
            }
        }

        // Groups follow in the order their first section appeared, so the weights still decide
        // what comes near the top.
        $menu = $ungrouped === [] ? [] : [['label' => '', 'sections' => $ungrouped]];
        foreach ($groups as $label => $entries) {
            $menu[] = ['label' => $label, 'sections' => $entries];
        }

        return $menu;
    }

    public function getAlerts(): array
    {
        return $this->registry->getAlertManager()->getAllAlerts();
    }
}

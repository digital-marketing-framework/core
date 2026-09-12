<?php

namespace DigitalMarketingFramework\Core\Backend\Section;

use DigitalMarketingFramework\Core\Backend\Request;

/**
 * One entry in the row of links to a section's other actions.
 *
 * A subsection is a label and a route, nothing more: the actions of a section are already
 * shared between however many controllers serve it, and this only says which of them are worth
 * offering. Section and action are read off the route the same way a Section reads its name,
 * so neither has to be repeated.
 */
class SubSection implements SubSectionInterface
{
    protected string $section;

    protected string $action;

    /**
     * @param array<string> $additionalActions further actions belonging to this subsection, so
     *                                         that it stays marked while they are being used
     */
    public function __construct(
        protected string $title,
        protected string $route,
        protected array $additionalActions = [],
        protected string $icon = '',
        protected int $weight = 100,
    ) {
        $request = new Request($route);
        $this->section = $request->getSection();
        $this->action = $request->getInternalRoute();
    }

    public function getName(): string
    {
        return $this->action;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function matchesAction(string $action): bool
    {
        return $action === $this->action || in_array($action, $this->additionalActions, true);
    }
}

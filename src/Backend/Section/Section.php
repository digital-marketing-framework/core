<?php

namespace DigitalMarketingFramework\Core\Backend\Section;

use DigitalMarketingFramework\Core\Backend\Request;

class Section implements SectionInterface
{
    protected string $name;

    public function __construct(
        protected string $title,
        protected string $subTitle,
        protected string $route,
        protected string $description = '',
        protected string $icon = 'PKG:digital-marketing-framework/core/res/assets/icons/logo.svg',
        protected string $actionLabel = 'Show',
        protected int $weight = 100,
    ) {
        $req = new Request($route);
        $this->name = $req->getSection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubTitle(): string
    {
        return $this->subTitle;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getActionLabel(): string
    {
        return $this->actionLabel;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }
}

<?php

namespace DigitalMarketingFramework\Core\Model\Alert;

class AlertAction implements AlertActionInterface
{
    /**
     * @param array<string,mixed> $arguments
     */
    public function __construct(
        protected string $label,
        protected string $route,
        protected array $arguments = [],
        protected string $icon = '',
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }
}

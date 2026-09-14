<?php

namespace DigitalMarketingFramework\Core\Model\Alert;

interface AlertActionInterface
{
    public function getLabel(): string;

    /**
     * The backend route the action leads to. A route rather than a URL, because URLs are built
     * where the alert is rendered, the same way sections and subsections are linked.
     */
    public function getRoute(): string;

    /**
     * @return array<string,mixed>
     */
    public function getArguments(): array;

    /**
     * Name of an action icon, or an empty string for a button without one.
     */
    public function getIcon(): string;
}

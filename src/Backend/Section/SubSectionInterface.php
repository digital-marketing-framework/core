<?php

namespace DigitalMarketingFramework\Core\Backend\Section;

interface SubSectionInterface
{
    /**
     * Identifies the subsection within its section. The action it leads to, since two
     * subsections of one section cannot sensibly lead to the same place.
     */
    public function getName(): string;

    public function getSection(): string;

    public function getTitle(): string;

    public function getRoute(): string;

    public function getIcon(): string;

    public function getWeight(): int;

    /**
     * Whether this subsection is the one an action belongs to, so that it stays marked while
     * any of its actions is being used.
     */
    public function matchesAction(string $action): bool;
}

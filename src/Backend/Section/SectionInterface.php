<?php

namespace DigitalMarketingFramework\Core\Backend\Section;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;

interface SectionInterface
{
    public function getName(): string;

    public function getTitle(): string;

    public function getSubTitle(): string;

    public function getRoute(): string;

    public function getDescription(): string;

    public function getIcon(): string;

    public function getActionLabel(): string;

    public function getWeight(): int;

    /**
     * Whether the section is offered on the dashboard and in the section menu. Asked when those
     * are rendered, once the registry is complete. A hidden section's pages still answer when
     * opened directly.
     */
    public function enabled(RegistryInterface $registry): bool;
}

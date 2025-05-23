<?php

namespace DigitalMarketingFramework\Core\Backend\Section;

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
}

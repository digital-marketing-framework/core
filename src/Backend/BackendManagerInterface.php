<?php

namespace DigitalMarketingFramework\Core\Backend;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Backend\Section\SectionInterface;

interface BackendManagerInterface
{
    public function getResponse(Request $request): Response;

    public function setSection(SectionInterface $section): void;

    public function getAllSections(): array;

    public function getSection(string $name): ?SectionInterface;

    public function getSectionMenu(Request $request): array;

    public function getAlerts(): array;
}

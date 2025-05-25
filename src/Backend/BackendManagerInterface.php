<?php

namespace DigitalMarketingFramework\Core\Backend;

use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Backend\Section\SectionInterface;
use DigitalMarketingFramework\Core\Model\Alert\AlertInterface;

interface BackendManagerInterface
{
    public function getResponse(Request $request): Response;

    public function setSection(SectionInterface $section): void;

    /**
     * @return array<string,SectionInterface>
     */
    public function getAllSections(): array;

    public function getSection(string $name): ?SectionInterface;

    /**
     * @return array<array{route:string,label:string,active:bool}>
     */
    public function getSectionMenu(Request $request): array;

    /**
     * @return array<AlertInterface>
     */
    public function getAlerts(): array;
}

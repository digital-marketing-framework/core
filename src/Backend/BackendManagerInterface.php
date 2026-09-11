<?php

namespace DigitalMarketingFramework\Core\Backend;

use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Backend\Section\SectionInterface;
use DigitalMarketingFramework\Core\Backend\Section\SubSectionInterface;
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
     * The sections to switch between, grouped by subtitle: the dropdown shows only titles, and
     * "Field Names" beside "Field Definitions" is told apart by its area alone. The group
     * labelled '' holds the sections with no subtitle and is rendered without a heading.
     *
     * @return array<array{label:string,sections:array<array{route:string,label:string,active:bool}>}>
     */
    public function getSectionMenu(Request $request): array;

    public function addSubSection(SubSectionInterface $subSection): void;

    /**
     * Empty for a section with fewer than two subsections, since there is nothing to choose
     * between.
     *
     * @return array<array{route:string,label:string,icon:string,active:bool}>
     */
    public function getSubSectionMenu(Request $request): array;

    /**
     * @return array<AlertInterface>
     */
    public function getAlerts(): array;
}

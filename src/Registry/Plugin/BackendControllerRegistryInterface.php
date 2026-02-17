<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Backend\Controller\AjaxController\AjaxControllerInterface;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionControllerInterface;
use DigitalMarketingFramework\Core\Backend\UriRouteResolver\UriRouteResolverInterface;

interface BackendControllerRegistryInterface
{
    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerBackendSectionController(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteBackendSectionController(string $keyword): void;

    public function getBackendSectionController(string $keyword): ?SectionControllerInterface;

    /**
     * @return array<SectionControllerInterface>
     */
    public function getAllBackendSectionControllers(): array;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerBackendAjaxController(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteBackendAjaxController(string $keyword): void;

    public function getBackendAjaxController(string $keyword): ?AjaxControllerInterface;

    /**
     * @return array<AjaxControllerInterface>
     */
    public function getAllBackendAjaxControllers(): array;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerBackendUriRouteResolver(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteBackendUriRouteResolver(string $keyword): void;

    public function getBackendUriRouteResolver(string $keyword): ?UriRouteResolverInterface;

    /**
     * @return array<UriRouteResolverInterface>
     */
    public function getAllBackendUriRouteResolvers(): array;
}

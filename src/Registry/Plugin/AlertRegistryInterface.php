<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Alert\AlertHandlerInterface;
use DigitalMarketingFramework\Core\Alert\AlertManagerInterface;

interface AlertRegistryInterface
{
    public function getAlertManager(): AlertManagerInterface;

    public function setAlertManager(AlertManagerInterface $alertManager): void;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerAlertHandler(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteAlertHandler(string $keyword): void;

    public function getAlertHandler(string $keyword): ?AlertHandlerInterface;

    /**
     * @return array<AlertHandlerInterface>
     */
    public function getAllAlertHandlers(): array;
}

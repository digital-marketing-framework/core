<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Resource\Template\TemplateServiceInterface;

interface TemplateRegistryInterface
{
    public function getTemplateService(): TemplateServiceInterface;

    public function setTemplateService(TemplateServiceInterface $templateService): void;

    public function renderErrorMessage(string $error): string;
}

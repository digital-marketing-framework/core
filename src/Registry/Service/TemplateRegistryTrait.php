<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Resource\Template\TemplateService;
use DigitalMarketingFramework\Core\Resource\Template\TemplateServiceInterface;

trait TemplateRegistryTrait
{
    protected TemplateServiceInterface $templateService;

    protected TemplateServiceInterface $backendTemplateService;

    public function getTemplateService(): TemplateServiceInterface
    {
        if (!isset($this->templateService)) {
            $this->templateService = $this->createObject(TemplateService::class, [$this]);
        }

        return $this->templateService;
    }

    public function setTemplateService(TemplateServiceInterface $templateService): void
    {
        $this->templateService = $templateService;
    }

    public function getBackendTemplateService(): TemplateServiceInterface
    {
        if (!isset($this->backendTemplateService)) {
            $this->backendTemplateService = $this->createObject(TemplateService::class, [$this]);
        }

        return $this->backendTemplateService;
    }

    public function setBackendTemplateService(TemplateServiceInterface $backendTemplateService): void
    {
        $this->backendTemplateService = $backendTemplateService;
    }

    public function renderErrorMessage(string $error): string
    {
        $engine = $this->getTemplateEngine();
        $result = $engine->render(
            [
                'templateName' => $this->getTemplateService()->getErrorMessageTemplateName(),
                'template' => $error,
            ],
            [
                'error' => $error,
            ]
        );

        return $result !== '' ? $result : $error;
    }
}

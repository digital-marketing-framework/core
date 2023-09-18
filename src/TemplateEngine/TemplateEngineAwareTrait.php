<?php

namespace DigitalMarketingFramework\Core\TemplateEngine;

trait TemplateEngineAwareTrait
{
    protected TemplateEngineInterface $templateEngine;

    public function setTemplateEngine(TemplateEngineInterface $templateEngine): void
    {
        $this->templateEngine = $templateEngine;
    }
}

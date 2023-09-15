<?php

namespace DigitalMarketingFramework\Core\TemplateEngine;

trait TemplateEngineAwareTrait
{
    protected TemplateEngineInterface $templateEngine;

    public function setTemplateEngine(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }
}

<?php

namespace DigitalMarketingFramework\Core\TemplateEngine;

interface TemplateEngineAwareInterface
{
    public function setTemplateEngine(TemplateEngineInterface $templateEngine): void;
}

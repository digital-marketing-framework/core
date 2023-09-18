<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\TemplateEngine\DefaultTemplateEngine;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineInterface;

trait TemplateEngineRegistryTrait
{
    protected TemplateEngineInterface $templateEngine;

    public function getTemplateEngine(): TemplateEngineInterface
    {
        if (!isset($this->templateEngine)) {
            $this->templateEngine = new DefaultTemplateEngine();
        }

        return $this->templateEngine;
    }

    public function setTemplateEngine(TemplateEngineInterface $templateEngine): void
    {
        $this->templateEngine = $templateEngine;
    }

    public function getTemplateSchema(string $format): SchemaInterface
    {
        return $this->getTemplateEngine()->getSchema($format);
    }
}

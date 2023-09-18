<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineInterface;

interface TemplateEngineRegistryInterface
{
    public function getTemplateEngine(): TemplateEngineInterface;

    public function setTemplateEngine(TemplateEngineInterface $templateEngine): void;

    public function getTemplateSchema(string $format): SchemaInterface;
}

<?php

namespace DigitalMarketingFramework\Core\TemplateEngine;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

interface TemplateEngineInterface
{
    public const TYPE_PLAIN_TEXT = 'TEMPLATE_PLAIN_TEXT';

    public const TYPE_HTML = 'TEMPLATE_HTML';

    public const FORMAT_PLAIN_TEXT = 'plain';

    public const FORMAT_HTML = 'html';

    /**
     * @param array<string,mixed> $config
     * @param array<string,string|ValueInterface> $data
     */
    public function render(array $config, array $data): string;

    public function getSchema(string $format): SchemaInterface;
}

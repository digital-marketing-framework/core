<?php

namespace DigitalMarketingFramework\Core\TemplateEngine;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

interface TemplateEngineInterface
{
    public const TYPE_PLAIN_TEXT = 'TEMPLATE_PLAIN_TEXT';

    public const TYPE_HTML = 'TEMPLATE_HTML';

    public const FORMAT_PLAIN_TEXT = 'plain';

    public const FORMAT_HTML = 'html';

    /**
     * @param array<string,mixed> $config
     * @param array<string,mixed> $data
     */
    public function render(array $config, array $data, bool $frontend = true): string;

    public function getSchema(string $format): SchemaInterface;
}

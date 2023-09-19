<?php

namespace DigitalMarketingFramework\Core\TemplateEngine;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DefaultTemplateEngine implements TemplateEngineInterface
{
    public const KEYWORD_ALL_VALUES = 'all_values';

    public const KEY_TEMPLATE = 'template';

    public const DEFAULT_TEMPLATES = [
        TemplateEngineInterface::FORMAT_PLAIN_TEXT => '{' . self::KEYWORD_ALL_VALUES . '}',
        TemplateEngineInterface::FORMAT_HTML => '{' . self::KEYWORD_ALL_VALUES . '}',
    ];

    protected const TEMPLATE_LABELS = [
        TemplateEngineInterface::FORMAT_PLAIN_TEXT => 'Template (Plain Text)',
        TemplateEngineInterface::FORMAT_HTML => 'Template (HTML)',
    ];

    /**
     * @param array<string,mixed> $config
     * @param array<string,string|ValueInterface> $data
     */
    public function render(array $config, array $data): string
    {
        $template = $config[static::KEY_TEMPLATE];
        $result = GeneralUtility::parseSeparatorString($template);

        $allValues = '';
        foreach ($data as $field => $value) {
            $result = str_replace('{' . $field . '}', $value, $result);
            $allValues .= $field . ' = ' . $value . PHP_EOL;
        }

        return str_replace('{' . static::KEYWORD_ALL_VALUES . '}', $allValues, $result);
    }

    public function getSchema(string $format): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setSkipHeader(true);

        if (!isset(static::DEFAULT_TEMPLATES[$format])) {
            throw new DigitalMarketingFrameworkException(sprintf('unknown template format "%s"', $format));
        }

        $templateSchema = new StringSchema(static::DEFAULT_TEMPLATES[$format]);
        $templateSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_TEXT);
        $templateSchema->getRenderingDefinition()->setLabel(static::TEMPLATE_LABELS[$format]);
        $schema->addProperty(static::KEY_TEMPLATE, $templateSchema);

        return $schema;
    }
}

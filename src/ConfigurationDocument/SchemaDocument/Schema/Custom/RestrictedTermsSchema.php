<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value\ScalarValues;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Utility\ListUtility;

class RestrictedTermsSchema extends SwitchSchema
{
    public const KEY_ALL = 'all';

    public const KEY_NONE = 'none';

    public const KEY_BLACKLIST = 'blacklist';

    public const KEY_WHITELIST = 'whitelist';

    public const KEY_LIST = 'list';

    protected function addList(string $name, string $referencePath, string $referenceType): void
    {
        $listItemSchema = new StringSchema();
        $listItemSchema->getAllowedValues()->addReference($referencePath, $referenceType);
        $listItemSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $listItemSchema->getRenderingDefinition()->hideLabel(true);
        $listSchema = new ListSchema($listItemSchema);
        $listSchema->getRenderingDefinition()->setLabel($name);
        $container = new ContainerSchema();
        $container->addProperty(static::KEY_LIST, $listSchema);
        $this->addItem($name, $container);
    }

    public function __construct(
        string $referencePath,
        string $referenceType = ScalarValues::REFERENCE_TYPE_KEY,
        mixed $defaultValue = null
    ) {
        parent::__construct('restrictedTerms', $defaultValue);
        $this->addItem(static::KEY_ALL, new ContainerSchema());
        $this->addItem(static::KEY_NONE, new ContainerSchema());
        $this->addList(static::KEY_BLACKLIST, $referencePath, $referenceType);
        $this->addList(static::KEY_WHITELIST, $referencePath, $referenceType);
    }

    /**
     * @param array{type:string,config:array<string,array{list?:array<string>}>} $config
     *
     * @return array<string>
     */
    public static function getAllowedTerms(array $config): array
    {
        $type = static::getSwitchType($config);
        $list = ListUtility::flatten(static::getSwitchConfiguration($config)[static::KEY_LIST] ?? []);
        switch ($type) {
            case static::KEY_ALL:
                return ['*'];
            case static::KEY_NONE:
                return [];
            case static::KEY_BLACKLIST:
                $list = array_map(static function ($term) {
                    return '!' . $term;
                }, $list);
                array_unshift($list, '*');

                return $list;
            case static::KEY_WHITELIST:
                return $list;
            default:
                throw new DigitalMarketingFrameworkException(sprintf('Unknown mode for restricted terms "%s"', $type));
        }
    }

    /**
     * @param array<string> $allowedTerms
     */
    public static function isTermAllowed(array $allowedTerms, string $term): bool
    {
        if (in_array('!' . $term, $allowedTerms)) {
            return false;
        }

        return in_array('*', $allowedTerms) || in_array($term, $allowedTerms);
    }
}

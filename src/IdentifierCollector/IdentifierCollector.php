<?php

namespace DigitalMarketingFramework\Core\IdentifierCollector;

use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Helper\ConfigurationTrait;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryInterface;

abstract class IdentifierCollector extends Plugin
{
    use ConfigurationTrait;

    public function __construct(
        string $keyword,
        IdentifierCollectorRegistryInterface $registry,
        protected ConfigurationInterface $identifiersConfiguration
    ) {
        parent::__construct($keyword, $registry);
        $this->configuration = $identifiersConfiguration->getIdentifierCollectorConfiguration($this->getKeyword());
    }

    abstract public function addContext(ContextInterface $source, WriteableContextInterface $target): void;
    abstract public function getIdentifier(ContextInterface $context): ?IdentifierInterface;

    public static function getDefaultConfiguration(): array
    {
        return [];
    }
}

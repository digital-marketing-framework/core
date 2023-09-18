<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\IdentifierCollector\IdentifierCollectorInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

interface IdentifierCollectorRegistryInterface extends PluginRegistryInterface
{
    public function getIdentifierCollector(string $keyword, ConfigurationInterface $configuration): ?IdentifierCollectorInterface;

    /**
     * @return array<IdentifierCollectorInterface>
     */
    public function getAllIdentifierCollectors(ConfigurationInterface $configuration): array;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerIdentifierCollector(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteIdentifierCollector(string $keyword): void;

    public function getIdentifierCollectorSchema(): SchemaInterface;
}

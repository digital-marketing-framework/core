<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Discovery;

interface StaticConfigurationDocumentDiscoveryInterface
{
    /**
     * @return array<string>
     */
    public function getIdentifiers(): array;

    public function match(string $identifier): bool;

    public function exists(string $identifier): bool;

    public function readonly(string $identifier): bool;

    public function getContent(string $identifier, bool $metaDataOnly = false): ?string;

    public function setContent(string $identifier, string $content): void;
}

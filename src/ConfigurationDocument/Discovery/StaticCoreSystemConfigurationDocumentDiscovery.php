<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Discovery;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class StaticCoreSystemConfigurationDocumentDiscovery extends StaticSystemConfigurationDocumentDiscovery
{
    public function getIdentifiers(): array
    {
        return ['SYS:defaults', 'SYS:reset'];
    }

    /**
     * @return array<string,mixed>
     */
    protected function buildDefaults(SchemaDocument $schemaDocument): array
    {
        return $this->schemaProcessor->getDefaultValue($schemaDocument);
    }

    /**
     * @return array<string,mixed>
     */
    protected function buildReset(SchemaDocument $schemaDocument): array
    {
        $defaults = $this->buildDefaults($schemaDocument);
        $reset = [];
        foreach (array_keys($defaults) as $key) {
            $reset[$key] = null;
        }

        return $reset;
    }

    protected function buildContent(string $identifier, SchemaDocument $schemaDocument): array
    {
        return match ($identifier) {
            'SYS:defaults' => $this->buildDefaults($schemaDocument),
            'SYS:reset' => $this->buildReset($schemaDocument),
            default => throw new DigitalMarketingFrameworkException(sprintf('Unknown system configuration document identifier "%s"', $identifier)),
        };
    }

    protected function getConfigurationDocumentName(string $identifier): string
    {
        return match ($identifier) {
            'SYS:defaults' => 'Defaults',
            'SYS:reset' => 'Reset',
            default => throw new DigitalMarketingFrameworkException(sprintf('Unknown system configuration document identifier "%s"', $identifier)),
        };
    }
}

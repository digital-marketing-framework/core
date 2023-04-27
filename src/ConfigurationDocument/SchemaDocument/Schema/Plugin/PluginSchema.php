<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class PluginSchema extends ContainerSchema
{
    public const TYPE = '';

    public function __construct(
        RegistryInterface $registry,
        ?string $onlyKeyword = null
    ) {
        parent::__construct();
        $this->init();
        foreach ($registry->getAllPluginClasses($this->getPluginInterface()) as $keyword => $class) {
            if ($onlyKeyword === null || $onlyKeyword === $keyword) {
                $this->addPlugin($keyword, $class);
            }
        }
    }

    protected function init(): void
    {
    }

    abstract protected function getPluginInterface(): string;

    abstract protected function processPlugin(string $keyword, string $class): void;

    public function addPlugin(string $keyword, string $class): void
    {
        $this->validatePluginClass($class);
        $this->processPlugin($keyword, $class);
    }

    protected function validatePluginClass(string $class): void
    {
        if (!class_exists($class)) {
            throw new DigitalMarketingFrameworkException(sprintf('class "%s" does not exist.'), $class);
        }
        $interface = $this->getPluginInterface();
        if (!in_array($interface, class_implements($class))) {
            throw new DigitalMarketingFrameworkException(sprintf('class "%s" has to implement interface "%s".', $class, $interface));
        }
    }

    public function getCustomType(): ?string
    {
        return static::TYPE ?: null;
    }
}

<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Cache\DataCacheAwareInterface;
use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationDocumentManagerRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryTrait;

class Registry implements RegistryInterface
{
    use GlobalConfigurationRegistryTrait;

    use LoggerFactoryRegistryTrait;
    use ContextRegistryTrait;
    use CacheRegistryTrait;
    use ConfigurationDocumentManagerRegistryTrait;

    use ConfigurationResolverRegistryTrait;
    use IdentifierCollectorRegistryTrait;

    protected function processObjectAwareness(object $object): void
    {
        if ($object instanceof GlobalConfigurationAwareInterface) {
            $object->setGlobalConfiguration($this->getGlobalConfiguration());
        }
        if ($object instanceof LoggerAwareInterface) {
            $logger = $this->getLoggerFactory()->getLogger(get_class($object));
            $object->setLogger($logger);
        }
        if ($object instanceof ContextAwareInterface) {
            $object->setContext($this->getContext());
        }
        if ($object instanceof DataCacheAwareInterface) {
            $object->setCache($this->getCache());
        }
    }

    protected function createObject(string $class, array $arguments = []): object
    {
        if (!class_exists($class)) {
            throw new RegistryException('Class "' . $class . '" is unknown!');
        }
        $object = new $class(...$arguments);
        $this->processObjectAwareness($object);
        return $object;
    }

    protected function classValidation(string $class, string $interface): void
    {
        if (!class_exists($class)) {
            throw new RegistryException('class "' . $class . '" does not exist.');
        }
        if (!in_array($interface, class_implements($class))) {
            throw new RegistryException('class "' . $class . '" has to implement interface "' . $interface . '".');
        }
    }

    protected function interfaceValidation(string $interface, string $parentInterface): void
    {
        if (!interface_exists($interface)) {
            throw new RegistryException('interface "' . $interface . '" does not exist.');
        }
        if (!is_subclass_of($interface, $parentInterface, true)) {
            throw new RegistryException('interface "' . $interface . '" has to extend "' . $parentInterface . '".');
        }
    }

    public function getIdentifierDefaultConfiguration(): array
    {
        $defaultIdentifierConfiguration = [];
        $defaultIdentifierConfiguration[ConfigurationInterface::KEY_IDENTIFIER_COLLECTORS] = $this->getIdentifierCollectorDefaultConfigurations();
        return $defaultIdentifierConfiguration;
    }

    public function getDefaultConfiguration(): array
    {
        return [
            ConfigurationInterface::KEY_DATA_MAPS => [],
            ConfigurationInterface::KEY_VALUE_MAPS => [],
            ConfigurationInterface::KEY_IDENTIFIER => $this->getIdentifierDefaultConfiguration(),
        ];
    }

    public function getConfigurationSchema(): array
    {
        return [];
    }
}

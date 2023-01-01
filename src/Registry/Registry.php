<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Cache\DataCache;
use DigitalMarketingFramework\Core\Cache\DataCacheAwareInterface;
use DigitalMarketingFramework\Core\Cache\DataCacheInterface;
use DigitalMarketingFramework\Core\Cache\NonPersistentCache;
use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\RequestContext;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;
use DigitalMarketingFramework\Core\Log\NullLoggerFactory;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryTrait;

abstract class Registry implements RegistryInterface
{
    use LoggerFactoryRegistryTrait;
    use ContextRegistryTrait;
    use CacheRegistryTrait;
    use GlobalConfigurationRegistryTrait;
    use ConfigurationResolverRegistryTrait;
    use IdentifierCollectorRegistryTrait;

    public function __construct(
        protected LoggerFactoryInterface $loggerFactory = new NullLoggerFactory(),
        protected ContextInterface $context = new RequestContext(),
        protected DataCacheInterface $cache = new DataCache(new NonPersistentCache()),
        protected ConfigurationInterface $globalConfiguration = new Configuration([]),
    ) {
    }

    protected function processObjectAwareness(object $object): void
    {
        if ($object instanceof LoggerAwareInterface) {
            $logger = $this->loggerFactory->getLogger(get_class($object));
            $object->setLogger($logger);
        }
        if ($object instanceof ContextAwareInterface) {
            $object->setContext($this->context);
        }
        if ($object instanceof DataCacheAwareInterface) {
            $object->setCache($this->cache);
        }
        if ($object instanceof GlobalConfigurationAwareInterface) {
            $object->setGlobalConfiguration($this->globalConfiguration);
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
}

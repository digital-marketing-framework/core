<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;
use DigitalMarketingFramework\Core\Log\NullLoggerFactory;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\RequestRegistryTrait;
use DigitalMarketingFramework\Core\Request\DefaultRequest;
use DigitalMarketingFramework\Core\Request\RequestAwareInterface;
use DigitalMarketingFramework\Core\Request\RequestInterface;

abstract class Registry implements RegistryInterface
{
    use LoggerFactoryRegistryTrait;
    use RequestRegistryTrait;

    public function __construct(
        protected LoggerFactoryInterface $loggerFactory = new NullLoggerFactory(),
        protected RequestInterface $request = new DefaultRequest(),
    ) {
    }

    protected function processObjectAwareness(object $object): void
    {
        if ($object instanceof LoggerAwareInterface) {
            $logger = $this->loggerFactory->getLogger(get_class($object));
            $object->setLogger($logger);
        }
        if ($object instanceof RequestAwareInterface) {
            $object->setRequest($this->request);
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

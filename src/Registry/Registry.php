<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareInterface;
use DigitalMarketingFramework\Core\Cache\DataCacheAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareInterface;
use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\SchemaProcessorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ApiRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\AssetServiceRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationDocumentManagerRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationSchemaRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\DataPrivacyManagerRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\FileStorageRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationSchemaRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ResourceServiceRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ScriptAssetsRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\StaticConfigurationDocumentRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\TemplateEngineRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\TemplateRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\VendorResourceServiceRegistryTrait;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareInterface;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineAwareInterface;

class Registry implements RegistryInterface
{
    use GlobalConfigurationRegistryTrait;
    use ScriptAssetsRegistryTrait;
    use GlobalConfigurationSchemaRegistryTrait;
    use ResourceServiceRegistryTrait;
    use TemplateRegistryTrait;
    use DataPrivacyManagerRegistryTrait;

    use LoggerFactoryRegistryTrait;
    use ContextRegistryTrait;
    use CacheRegistryTrait;
    use ConfigurationSchemaRegistryTrait;
    use ConfigurationDocumentManagerRegistryTrait;
    use FileStorageRegistryTrait;

    use AssetServiceRegistryTrait;
    use TemplateEngineRegistryTrait;
    use VendorResourceServiceRegistryTrait;
    use StaticConfigurationDocumentRegistryTrait;

    use SchemaProcessorRegistryTrait;
    use DataProcessorRegistryTrait;
    use IdentifierCollectorRegistryTrait;

    use ApiRegistryTrait;

    protected RegistryCollectionInterface $registryCollection;

    public function getRegistryCollection(): RegistryCollectionInterface
    {
        if (!isset($this->registryCollection)) {
            throw new RegistryException('No registry collection found');
        }

        return $this->registryCollection;
    }

    public function setRegistryCollection(RegistryCollectionInterface $registryCollection): void
    {
        $this->registryCollection = $registryCollection;
    }

    public function processObjectAwareness(object $object): void
    {
        if ($object instanceof GlobalConfigurationAwareInterface) {
            $object->setGlobalConfiguration($this->getGlobalConfiguration());
        }

        if ($object instanceof LoggerAwareInterface) {
            $logger = $this->getLoggerFactory()->getLogger($object::class);
            $object->setLogger($logger);
        }

        if ($object instanceof ContextAwareInterface) {
            $object->setContext($this->getContext());
        }

        if ($object instanceof DataCacheAwareInterface) {
            $object->setCache($this->getCache());
        }

        if ($object instanceof DataProcessorAwareInterface) {
            $object->setDataProcessor($this->getDataProcessor());
        }

        if ($object instanceof FileStorageAwareInterface) {
            $object->setFileStorage($this->getFileStorage());
        }

        if ($object instanceof TemplateEngineAwareInterface) {
            $object->setTemplateEngine($this->getTemplateEngine());
        }

        if ($object instanceof SchemaProcessorAwareInterface) {
            $object->setSchemaProcessor($this->getSchemaProcessor());
        }

        if ($object instanceof ConfigurationDocumentManagerAwareInterface) {
            $object->setConfigurationDocumentManager($this->getConfigurationDocumentManager());
        }

        if ($object instanceof ConfigurationDocumentParserAwareInterface) {
            $object->setConfigurationDocumentParser($this->getConfigurationDocumentParser());
        }

        if ($object instanceof EndPointStorageAwareInterface) {
            $object->setEndPointStorage($this->getEndPointStorage());
        }

        if ($object instanceof DataPrivacyManagerAwareInterface) {
            $object->setDataPrivacyManager($this->getDataPrivacyManager());
        }
    }

    public function addServiceContext(WriteableContextInterface $context): void
    {
        $this->getDataPrivacyManager()->addContext($context);
    }

    public function createObject(string $class, array $arguments = []): object
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

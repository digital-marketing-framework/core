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
use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerAwareInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\AlertRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\BackendControllerRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\CleanupRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\NotificationRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\SchemaProcessorRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ApiRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\AssetServiceRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\BackendTemplatingRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationDocumentManagerRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationSchemaRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\DataPrivacyManagerRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\EnvironmentRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\FileStorageRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationSchemaRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ResourceServiceRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\ScriptAssetsRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\StaticConfigurationDocumentRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\TemplateEngineRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\TemplateRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\TestCaseRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Service\VendorResourceServiceRegistryTrait;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareInterface;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineAwareInterface;
use DigitalMarketingFramework\Core\TestCase\TestCaseManagerAwareInterface;

class Registry implements RegistryInterface
{
    use GlobalConfigurationRegistryTrait;
    use EnvironmentRegistryTrait;
    use ScriptAssetsRegistryTrait;
    use GlobalConfigurationSchemaRegistryTrait;
    use ResourceServiceRegistryTrait;
    use TemplateRegistryTrait;
    use DataPrivacyManagerRegistryTrait;

    use LoggerFactoryRegistryTrait;
    use NotificationRegistryTrait;
    use AlertRegistryTrait;
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
    use TestCaseRegistryTrait;
    use BackendTemplatingRegistryTrait;
    use BackendControllerRegistryTrait;
    use CleanupRegistryTrait;

    protected RegistryCollectionInterface $registryCollection;

    /**
     * If a project-specific initialization is necessary, this method can be used.
     * Called by the registry collection service.
     */
    public function init(): void
    {
    }

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

        if ($object instanceof NotificationManagerAwareInterface) {
            $object->setNotificationManager($this->getNotificationManager());
        }

        if ($object instanceof TestCaseManagerAwareInterface) {
            $object->setTestCaseManager($this->getTestCaseManager());
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

        $arguments = array_map(fn (mixed $arg) => $arg instanceof ProxyArgument ? $arg() : $arg, $arguments);

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

    public function getHost(): string
    {
        $host = $this->getContext()->getHost();

        if ($host === null || $host === '') {
            $host = $this->getGlobalConfiguration()->get('core')[CoreGlobalConfigurationSchema::KEY_ENVIRONMENT]
                ?? CoreGlobalConfigurationSchema::DEFAULT_ENVIRONMENT;
        }

        return $host;
    }
}

<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Alert\AlertManager;
use DigitalMarketingFramework\Core\Alert\AlertManagerInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolver;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\ContextStack;
use DigitalMarketingFramework\Core\Context\ContextStackInterface;
use DigitalMarketingFramework\Core\Context\RequestContext;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Notification\NotificationManager;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;

class RegistryCollection implements RegistryCollectionInterface
{
    protected ContextStackInterface $context;

    protected NotificationManagerInterface $notificationManager;

    protected AlertManagerInterface $alertManager;

    /**
     * @param array{core?:RegistryInterface,distributor?:RegistryInterface,collector?:RegistryInterface} $collection
     */
    public function __construct(
        protected array $collection = [],
    ) {
        $this->fetchRegistries();
        foreach ($this->collection as $registry) {
            $registry->init();
        }
    }

    /**
     * A project-specific registry collection might be able to fetch all relevant registries itself.
     * This can be done in this method, which will also trigger the initialization of the registries automatically.
     */
    protected function fetchRegistries(): void
    {
    }

    public function addRegistry(string $domain, RegistryInterface $registry): void
    {
        $this->collection[$domain] = $registry;
        $registry->setRegistryCollection($this);
    }

    public function getRegistry(string $domain = RegistryDomain::CORE): RegistryInterface
    {
        if (!isset($this->collection[$domain])) {
            throw new RegistryException(sprintf('Registry for domain "%s" not found', $domain));
        }

        return $this->collection[$domain];
    }

    public function getRegistryByClass(string $class): RegistryInterface
    {
        foreach ($this->collection as $registry) {
            if ($registry instanceof $class) {
                return $registry;
            }
        }

        throw new RegistryException(sprintf('Registry "%s" not found', $class));
    }

    public function getAllRegistries(): array
    {
        if (!isset($this->collection[RegistryDomain::CORE])) {
            throw new DigitalMarketingFrameworkException('Registry collection must have at least the core registry added!');
        }

        return $this->collection;
    }

    public function getContext(): ContextStackInterface
    {
        if (!isset($this->context)) {
            $this->context = new ContextStack();
            $this->context->pushContext(new RequestContext());
        }

        return $this->context;
    }

    public function setContext(ContextInterface $context): void
    {
        $contextStack = $this->getContext();
        $contextStack->clearStack();
        $contextStack->pushContext($context);
    }

    public function pushContext(ContextInterface $context): void
    {
        $this->getContext()->pushContext($context);
    }

    public function popContext(): ?ContextInterface
    {
        return $this->getContext()->popContext();
    }

    public function getNotificationManager(): NotificationManagerInterface
    {
        if (!isset($this->notificationManager)) {
            $this->notificationManager = $this->getRegistry()->createObject(NotificationManager::class, [$this->getRegistry()]);
        }

        return $this->notificationManager;
    }

    public function setNotificationManager(NotificationManagerInterface $notificationManager): void
    {
        $this->notificationManager = $notificationManager;
    }

    public function getAlertManager(): AlertManagerInterface
    {
        if (!isset($this->alertManager)) {
            $this->alertManager = $this->getRegistry()->createObject(AlertManager::class, [$this->getRegistry()]);
        }

        return $this->alertManager;
    }

    public function setAlertManager(AlertManagerInterface $alertManager): void
    {
        $this->alertManager = $alertManager;
    }

    public function getConfigurationSchemaDocument(): SchemaDocument
    {
        $document = new SchemaDocument();
        foreach ($this->collection as $registry) {
            $registry->addConfigurationSchemaDocument($document);
        }

        return $document;
    }

    public function getGlobalConfigurationSchemaDocument(): SchemaDocument
    {
        $document = new SchemaDocument();
        foreach ($this->collection as $registry) {
            $registry->addGlobalConfigurationSchemaDocument($document);
        }

        return $document;
    }

    public function getFrontendScripts(bool $activeOnly = false): array
    {
        $frontendScripts = [];
        foreach ($this->collection as $registry) {
            foreach ($registry->getFrontendScripts($activeOnly) as $type => $typeScripts) {
                foreach ($typeScripts as $package => $paths) {
                    $scripts = $frontendScripts[$type][$package] ??= [];
                    array_push($scripts, ...$paths);
                    $frontendScripts[$type][$package] = array_unique($scripts);
                }
            }
        }

        return $frontendScripts;
    }

    public function getConfigurationEditorScripts(): array
    {
        $configurationEditorScripts = [];
        foreach ($this->collection as $registry) {
            foreach ($registry->getConfigurationEditorScripts() as $package => $paths) {
                $scripts = $configurationEditorScripts[$package] ?? [];
                array_push($scripts, ...$paths);
                $configurationEditorScripts[$package] = array_unique($scripts);
            }
        }

        return $configurationEditorScripts;
    }

    public function getFrontendSettings(): array
    {
        $frontendSettingsList = array_map(static fn (RegistryInterface $registry) => $registry->getFrontendSettings(), $this->collection);

        return ConfigurationUtility::mergeConfigurationStack($frontendSettingsList, excludeKeys: []);
    }

    public function getApiEntryRouteResolver(): EntryRouteResolverInterface
    {
        $entryRouteResolver = $this->getRegistry()->createObject(EntryRouteResolver::class);
        foreach ($this->collection as $registry) {
            foreach ($registry->getApiRouteResolvers() as $domain => $routeResolver) {
                $entryRouteResolver->registerResolver($domain, $routeResolver);
            }
        }

        return $entryRouteResolver;
    }
}

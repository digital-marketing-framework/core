<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\DataSource\CoreDataSourceManager;
use DigitalMarketingFramework\Core\DataSource\CoreDataSourceStorageInterface;
use DigitalMarketingFramework\Core\DataSource\DataSourceManagerInterface;
use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryTrait;
use DigitalMarketingFramework\Core\Registry\RegistryCollectionInterface;

trait DataSourceRegistryTrait
{
    use PluginRegistryTrait;

    /** @var ?DataSourceManagerInterface<DataSourceInterface> */
    protected ?DataSourceManagerInterface $coreDataSourceManager = null;

    abstract public function getRegistryCollection(): RegistryCollectionInterface;

    /**
     * @return DataSourceManagerInterface<DataSourceInterface>
     */
    public function getCoreDataSourceManager(): DataSourceManagerInterface
    {
        if ($this->coreDataSourceManager === null) {
            $this->coreDataSourceManager = $this->createObject(CoreDataSourceManager::class, [$this]);
        }

        return $this->coreDataSourceManager;
    }

    public function registerCoreSourceStorage(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(CoreDataSourceStorageInterface::class, $class, $additionalArguments, $keyword);
    }

    public function getAllCoreSourceStorages(): array
    {
        return $this->getAllPlugins(CoreDataSourceStorageInterface::class);
    }

    /**
     * @template T of DataSourceInterface
     *
     * @param DataSourceManagerInterface<T> $dataSourceManager
     */
    public function addDataSourceManager(DataSourceManagerInterface $dataSourceManager): void
    {
        $this->getRegistryCollection()->addDataSourceManager($dataSourceManager);
    }

    public function getDataSourceManagers(): array
    {
        return $this->getRegistryCollection()->getDataSourceManagers();
    }
}

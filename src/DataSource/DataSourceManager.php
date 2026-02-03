<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;

/**
 * @template DataSourceClass of DataSourceInterface
 *
 * @implements DataSourceManagerInterface<DataSourceClass>
 */
abstract class DataSourceManager implements DataSourceManagerInterface
{
    /**
     * @return array<DataSourceStorageInterface<DataSourceClass>>
     */
    abstract protected function getDataSourceStorages(): array;

    /**
     * @return ?DataSourceStorageInterface<DataSourceClass>
     */
    protected function getMatchingDataSourceStorage(string $id): ?DataSourceStorageInterface
    {
        foreach ($this->getDataSourceStorages() as $storage) {
            if ($storage->matches($id)) {
                return $storage;
            }
        }

        return null;
    }

    public function getDataSourceById(string $id, array $dataSourceContext): ?DataSourceInterface
    {
        $storage = $this->getMatchingDataSourceStorage($id);
        if (!$storage instanceof DataSourceStorageInterface) {
            return null;
        }

        return $storage->getDataSourceById($id, $dataSourceContext);
    }

    public function getAllDataSources(): array
    {
        $result = [];
        foreach ($this->getDataSourceStorages() as $storage) {
            foreach ($storage->getAllDataSources() as $source) {
                $result[] = $source;
            }
        }

        return $result;
    }

    public function getAllDataSourceVariants(): array
    {
        $result = [];
        foreach ($this->getDataSourceStorages() as $storage) {
            foreach ($storage->getAllDataSourceVariants() as $source) {
                $result[] = $source;
            }
        }

        return $result;
    }
}

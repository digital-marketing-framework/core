<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
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

    public function getAllDataSourceVariantIdentifiers(): array
    {
        $result = [];
        foreach ($this->getDataSourceStorages() as $storage) {
            foreach ($storage->getAllDataSourceVariantIdentifiers() as $identifier) {
                $result[] = $identifier;
            }
        }

        return $result;
    }

    public function getDataSourceVariantByIdentifier(string $identifier): ?DataSourceInterface
    {
        $storage = $this->getMatchingDataSourceStorage($identifier);

        return $storage?->getDataSourceVariantByIdentifier($identifier);
    }

    public function updateConfigurationDocument(DataSourceInterface $dataSource, string $document): void
    {
        $storage = $this->getMatchingDataSourceStorage($dataSource->getIdentifier());
        if (!$storage instanceof DataSourceStorageInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('No matching storage found for data source "%s"', $dataSource->getIdentifier()));
        }

        $storage->updateConfigurationDocument($dataSource, $document);
    }
}

<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;
use DigitalMarketingFramework\Core\Plugin\Plugin;

/**
 * @template DataSourceClass of DataSourceInterface
 * @implements DataSourceStorageInterface<DataSourceClass>
 */
abstract class DataSourceStorage extends Plugin implements DataSourceStorageInterface
{
    abstract public function getType(): string;
    abstract public function getDataSourceById(string $id, array $dataSourceContext): ?DataSourceInterface;
    abstract public function getAllDataSources(): array;

    protected function getInnerIdentifier(string $id): string
    {
        return substr($id, strlen($this->getType()) + 1);
    }

    protected function getOuterIdentifier(string $innerId): string
    {
        return $this->getType() . ':' . $innerId;
    }

    public function matches(string $id): bool
    {
        return str_starts_with($id, $this->getType() . ':');
    }
}

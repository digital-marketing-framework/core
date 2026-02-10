<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;
use DigitalMarketingFramework\Core\Plugin\Plugin;

/**
 * @template DataSourceClass of DataSourceInterface
 *
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

    public function getAllDataSourceVariants(): array
    {
        return $this->getAllDataSources();
    }

    public function getAllDataSourceVariantIdentifiers(): array
    {
        return array_map(
            static fn (DataSourceInterface $dataSource) => $dataSource->getIdentifier(),
            $this->getAllDataSourceVariants()
        );
    }

    public function getDataSourceVariantByIdentifier(string $identifier): ?DataSourceInterface
    {
        if (!$this->matches($identifier)) {
            return null;
        }

        foreach ($this->getAllDataSourceVariants() as $dataSource) {
            if ($dataSource->getIdentifier() === $identifier) {
                return $dataSource;
            }
        }

        return null;
    }

    public function updateConfigurationDocument(DataSourceInterface $dataSource, string $document): void
    {
        throw new DigitalMarketingFrameworkException(sprintf('updateConfigurationDocument() is not implemented for storage type "%s"', $this->getType()));
    }

    public function matches(string $id): bool
    {
        return str_starts_with($id, $this->getType() . ':');
    }
}

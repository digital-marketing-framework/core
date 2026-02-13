<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareInterface;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareTrait;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Model\DataSource\ApiEndPointDataSource;
use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

/**
 * @template DataSourceClass of DataSourceInterface
 *
 * @extends DataSourceStorage<DataSourceClass>
 */
abstract class AbstractApiEndPointDataSourceStorage extends DataSourceStorage implements EndPointStorageAwareInterface
{
    use EndPointStorageAwareTrait;

    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
    ) {
        parent::__construct($keyword);
    }

    public function getType(): string
    {
        return ApiEndPointDataSource::TYPE;
    }

    /**
     * @return DataSourceClass
     */
    abstract protected function createDataSource(EndPointInterface $endPoint): DataSourceInterface;

    /**
     * Filter for getAllDataSources() and getDataSourceByIdentifier().
     */
    abstract protected function filterEndPoint(EndPointInterface $endPoint): bool;

    /**
     * Filter for getAllDataSourceVariants().
     */
    abstract protected function filterEndPointVariant(EndPointInterface $endPoint): bool;

    public function getDataSourceByIdentifier(string $identifier): ?DataSourceInterface
    {
        if (!$this->matches($identifier)) {
            return null;
        }

        $name = $this->getInnerIdentifier($identifier);
        $endPoint = $this->endPointStorage->fetchByName($name);

        if (!$endPoint instanceof EndPointInterface || !$this->filterEndPoint($endPoint)) {
            return null;
        }

        return $this->createDataSource($endPoint);
    }

    public function getAllDataSources(): array
    {
        $result = [];
        foreach ($this->endPointStorage->fetchAll() as $endPoint) {
            if ($this->filterEndPoint($endPoint)) {
                $result[] = $this->createDataSource($endPoint);
            }
        }

        return $result;
    }

    public function getAllDataSourceVariants(): array
    {
        $result = [];
        foreach ($this->endPointStorage->fetchAll() as $endPoint) {
            if ($this->filterEndPointVariant($endPoint)) {
                $result[] = $this->createDataSource($endPoint);
            }
        }

        return $result;
    }

    public function getAllDataSourceVariantIdentifiers(): array
    {
        $result = [];
        foreach ($this->endPointStorage->fetchAll() as $endPoint) {
            if ($this->filterEndPointVariant($endPoint)) {
                $result[] = $this->getOuterIdentifier($endPoint->getName());
            }
        }

        return $result;
    }

    public function getDataSourceVariantByIdentifier(string $identifier, bool $maintenanceMode = false): ?DataSourceInterface
    {
        if (!$this->matches($identifier)) {
            return null;
        }

        $name = $this->getInnerIdentifier($identifier);
        $endPoint = $this->endPointStorage->fetchByName($name);

        if (!$endPoint instanceof EndPointInterface || !$this->filterEndPointVariant($endPoint)) {
            return null;
        }

        return $this->createDataSource($endPoint);
    }

    public function updateConfigurationDocument(DataSourceInterface $dataSource, string $document): void
    {
        if (!$dataSource instanceof ApiEndPointDataSource) {
            return;
        }

        $endPoint = $dataSource->getEndPoint();
        $endPoint->setConfigurationDocument($document);

        $this->endPointStorage->update($endPoint);
    }
}

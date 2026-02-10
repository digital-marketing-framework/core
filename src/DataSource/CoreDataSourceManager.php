<?php

namespace DigitalMarketingFramework\Core\DataSource;

use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

/**
 * @extends DataSourceManager<DataSourceInterface>
 */
class CoreDataSourceManager extends DataSourceManager
{
    /**
     * @var ?array<DataSourceStorageInterface<DataSourceInterface>>
     */
    protected ?array $sourceStorages = null;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    protected function getDataSourceStorages(): array
    {
        if ($this->sourceStorages === null) {
            $this->sourceStorages = $this->registry->getAllCoreSourceStorages();
        }

        return $this->sourceStorages;
    }
}

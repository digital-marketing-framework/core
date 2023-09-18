<?php

namespace DigitalMarketingFramework\Core\Model\DataSet;

use DigitalMarketingFramework\Core\Context\WriteableContext;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class DataSet implements DataSetInterface
{
    protected DataInterface $data;

    protected ConfigurationInterface $configuration;

    protected WriteableContextInterface $context;

    /**
     * @param array<string,string|ValueInterface> $data The form fields and their values as associative array
     * @param array<array<string,mixed>> $configurationList An array of (override) configurations
     * @param array<string,mixed> $context The context needed for processing the submission
     */
    public function __construct(array $data, array $configurationList = [], array $context = [])
    {
        $this->data = new Data($data);
        $this->configuration = new Configuration($configurationList);
        $this->context = new WriteableContext($context);
    }

    public function getData(): DataInterface
    {
        return $this->data;
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    public function getContext(): WriteableContextInterface
    {
        return $this->context;
    }
}

<?php

namespace DigitalMarketingFramework\Core\Model\DataSet;

use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Context\Context;
use DigitalMarketingFramework\Core\Model\Context\ContextInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class DataSet implements DataSetInterface
{
    protected DataInterface $data;
    protected ConfigurationInterface $configuration;
    protected ContextInterface $context;

    /**
     * @param array $data The form fields and their values as associative array
     * @param array $configurationList An array of (override) configurations
     * @param array $context The context needed for processing the submission
     */
    public function __construct(array $data, array $configurationList = [], array $context = [])
    {
        $this->data = new Data($data);
        $this->configuration = new Configuration($configurationList);
        $this->context = new Context($context);
    }

    public function getData(): DataInterface
    {
        return $this->data;
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    public function getContext(): ContextInterface
    {
        return $this->context;
    }
}

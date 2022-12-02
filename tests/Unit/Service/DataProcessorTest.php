<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Service;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use DigitalMarketingFramework\Core\Service\DataProcessor;
use DigitalMarketingFramework\Core\Service\DataProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataProcessorTest extends TestCase
{
    protected ConfigurationResolverRegistryInterface&MockObject $registry;

    protected array $contentResolverDataList;

    protected DataProcessorInterface $subject;

    public function setup(): void
    {
        parent::setUp();

        $this->registry = $this->createMock(ConfigurationResolverRegistryInterface::class);

        $this->registry->method('getContentResolver')->will($this->returnCallback(function($keyword, $configuration) {
            if ($keyword !== 'general') {
                return null;
            }
            foreach ($this->contentResolverDataList as $contentResolverData) {
                if ($contentResolverData['config'] === $configuration) {
                    return $contentResolverData['resolver'];
                }
            }
            return null;
        })); 
    }

    protected function addContentResolver($config, $return): void
    {
        $contentResolver = $this->createMock(GeneralContentResolver::class);
        $contentResolver->method('resolve')->willReturn($return);
        $this->contentResolverDataList[] = [
            'config' => $config,
            'resolver' => $contentResolver,
        ];
    }

    /** @test */
    public function emptyConfigurationProducesEmptyData(): void
    {
        $configuration = [];
        $data = [];

        $this->subject = new DataProcessor($this->registry, $configuration);

        $output = $this->subject->process(new Data($data));
        $this->assertTrue($output instanceof DataInterface);
        $this->assertEmpty($output->toArray());
    }

    /** @test */
    public function fieldsConfigurationGetsProcessedAndNullValuesIgnored(): void
    {
        $configuration = [
            DataProcessorInterface::KEY_FIELDS => [
                'field1' => 'someConfiguration',
                'field2' => ['someOther' => 'configuration'],
                'field3' => 'yetAnotherConfiguration',
            ],
        ];
        $data = [];

        $this->addContentResolver('someConfiguration', 'processedValue1');
        $this->addContentResolver(['someOther' => 'configuration'], null);
        $this->addContentResolver('yetAnotherConfiguration', 'processedValue3');

        $this->subject = new DataProcessor($this->registry, $configuration);

        $output = $this->subject->process(new Data($data));
        $this->assertTrue($output instanceof DataInterface);
        $this->assertEquals([
            'field1' => 'processedValue1',
            'field3' => 'processedValue3',
        ], $output->toArray());
    }

    /** @test */
    public function passThroughWillUsePassthroughResolver(): void
    {
        $configuration = [
            DataProcessorInterface::KEY_PASSTHROUGH_FIELDS => true,
        ];
        $data = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];

        $this->addContentResolver(
            [
                'passthroughFields' => true,
            ], 
            new Data([
                'field1' => 'value1',
                'field2' => 'value2',
            ])
        );

        $this->subject = new DataProcessor($this->registry, $configuration);

        $output = $this->subject->process(new Data($data));
        $this->assertTrue($output instanceof DataInterface);
        $this->assertEquals([
            'field1' => 'value1',
            'field2' => 'value2',
        ], $output->toArray());
    }
}

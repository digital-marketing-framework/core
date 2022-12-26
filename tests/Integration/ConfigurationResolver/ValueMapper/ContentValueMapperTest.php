<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\ContentValueMapper;

/**
 * @covers ContentValueMapper
 */
class ContentValueMapperTest extends AbstractValueMapperTest
{
    /** @test */
    public function constantStringValue(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'content' => 'value1b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function fieldValue(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'content' => ['field' => 'field2'],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function valueDependentConstantStringValue(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'value1' => ['content' => 'value1b'],
            'value2' => ['content' => 'value2b'],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function valueDependentFieldValue(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'value1' => ['content' => ['field' => 'field2']],
            'value2' => ['content' => ['field' => 'field3']],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function conditionalContent(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $this->fieldValue = 'value1';
        $config = [
            'content' => [
                'if' => [
                    'field2' => 'value2',
                    'then' => 'thenValue',
                    'else' => 'elseValue',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('thenValue', $result);
    }
}

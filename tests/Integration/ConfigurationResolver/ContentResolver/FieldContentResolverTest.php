<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers FieldContentResolver
 */
class FieldContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function fieldDoesNotExist(): void
    {
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function fieldExists(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function fieldIsEmpty(): void
    {
        $this->data['field1'] = '';
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNotNull($result);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function fieldHasMultiValue(): void
    {
        $this->data['field1'] = new MultiValue(['value1', 'value2']);
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1','value2'], $result);
    }

    /** @test */
    public function fieldHasEmptyMultiValue(): void
    {
        $this->data['field1'] = new MultiValue();
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEmpty($result);
    }

    /** @test */
    public function fieldHasToBeProcessed(): void
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'field' => [
                'if' => [
                    'field2' => 'value2',
                    'then' => 'field1',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function fieldHasToBeProcessedButResolvesToNull(): void
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            'field' => [
                'if' => [
                    'field2' => 'value3',
                    'then' => 'field1',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function fieldHasToBeProcessedRecursively(): void
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'field1';
        $config = [
            'field' => ['field' => 'field2']
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }
}

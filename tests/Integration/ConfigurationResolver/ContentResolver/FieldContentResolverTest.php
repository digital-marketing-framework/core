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
    public function fieldDoesNotExist()
    {
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function fieldExists()
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function fieldIsEmpty()
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
    public function fieldHasMultiValue()
    {
        $this->data['field1'] = new MultiValue(['value1', 'value2']);
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1','value2'], $result);
    }

    /** @test */
    public function fieldHasEmptyMultiValue()
    {
        $this->data['field1'] = new MultiValue();
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEmpty($result);
    }
}

<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers GeneralValueMapper
 */
class GeneralValueMapperTest extends AbstractValueMapperTest
{
    /** @test */
    public function mapNull(): void
    {
        $this->fieldValue = null;
        $config = [
            'value0' => 'value0b',
            'value1' => 'value1b',
            'value2' => 'value2b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function mapMatches(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'value0' => 'value0b',
            'value1' => 'value1b',
            'value2' => 'value2b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function mapDoesNotMatch(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'value0' => 'value0b',
            'value2' => 'value2b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function mapMultiValueAllMatch(): void
    {
        $this->fieldValue = new MultiValue(['value1', 'value2', 'value3']);
        $config = [
            'value1' => 'value1b',
            'value2' => 'value2b',
            'value3' => 'value3b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1b', 'value2b', 'value3b'], $result);
    }

    /** @test */
    public function mapMultiValueSomeMatch(): void
    {
        $this->fieldValue = new MultiValue(['value1', 'value2', 'value3']);
        $config = [
            'value1' => 'value1b',
            'value3' => 'value3b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1b', 'value2', 'value3b'], $result);
    }

    /** @test */
    public function mapMultiValueNoneMatch(): void
    {
        $this->fieldValue = new MultiValue(['value1', 'value2', 'value3']);
        $config = [
            'value4' => 'value4b',
            'value5' => 'value5b',
            'value6' => 'value6b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2', 'value3'], $result);
    }

    /** @test */
    public function mapNestedMultiValue(): void
    {
        $this->fieldValue = new MultiValue([
            'key1' => 'value1',
            'key2' => new MultiValue(),
            'key3' => new MultiValue([
                'key3_1' => 'value3_1',
                'key3_2' => new MultiValue([
                    'key_3_2_1' => 'value3_2_1',
                    'key_3_2_2' => 'value3_2_2',
                ]),
            ]),
        ]);
        $config = [
            'value1' => 'value1b',
            'value3_1' => 'value3_1b',
            'value3_2_1' => 'value3_2_1b',
            'value3_2_2' => 'value3_2_2b',
        ];
        $result = $this->runResolverProcess($config);

        $expected = new MultiValue([
            'key1' => 'value1b',
            'key2' => new MultiValue(),
            'key3' => new MultiValue([
                'key3_1' => 'value3_1b',
                'key3_2' => new MultiValue([
                    'key_3_2_1' => 'value3_2_1b',
                    'key_3_2_2' => 'value3_2_2b',
                ]),
            ]),
        ]);

        $this->assertMultiValueEquals($expected, $result);
    }
}

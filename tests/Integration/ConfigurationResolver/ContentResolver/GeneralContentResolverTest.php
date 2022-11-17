<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers GeneralContentResolver
 */
class GeneralContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function singleValue(): void
    {
        $config = 'value1';
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function concatenated(): void
    {
        $config = [
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1value2', $result);
    }

    /** @test */
    public function concatenatedWithGlue(): void
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2', $result);
    }

    /** @test */
    public function concatenatedWithGlueAtTheEnd(): void
    {
        $config = [
            1 => 'value1',
            2 => 'value2',
            'glue' => ',',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2', $result);
    }

    /** @test */
    public function concatenateWithGlueThatNeedsToBeResolvedUsingIfThen(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'glue' => [
                'if' => [
                    'field1' => 'value1',
                    'then' => '-',
                    'else' => '+',
                ],
            ],
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1-value2', $result);
    }

    /** @test */
    public function concatenateWithGlueThatNeedsToBeResolvedUsingIfElse(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'glue' => [
                'if' => [
                    'field1' => 'value2',
                    'then' => '-',
                    'else' => '+',
                ],
            ],
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1+value2', $result);
    }

    /** @test */
    public function concatenateWithGlueThatResolvesToNull(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'glue' => [
                'if' => [
                    'field1' => 'value1',
                    'else' => ';',
                ],
            ],
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1value2', $result);
    }

    /** @test */
    public function concatenateWhereGlueDoesNotGetPassedToSubResolvers(): void
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => [
                1 => 'value2.1',
                2 => 'value2.2',
            ],
            3 => 'value3',
            4 => [
                'glue' => ';',
                1 => 'value4.1',
                2 => 'value4.2',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2.1value2.2,value3,value4.1;value4.2', $result);
    }

    /** @test */
    public function concatenateWithGlueThatNeedsToBeParsed(): void
    {
        $config = [
            'glue' => '\\s',
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("value1 value2", $result);
    }

    /** @test */
    public function concatenateWithGlueAndEmptyValues(): void
    {
        $config = [
            'glue' => ',',
            1 => '',
            2 => '',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function concatenateWithGlueAndEmptyFirstValue(): void
    {
        $config = [
            'glue' => ',',
            1 => '',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function concatenateWithGlueAndEmptySecondValue(): void
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => '',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function singleMultiValueWithGlue(): void
    {
        $config = [
            'glue' => ',',
            1 => [
                'multiValue' => ['value1', 'value2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2'], $result);
    }

    /** @test */
    public function emptyScalarValueAndNonEmptyMultiValueWithGlue(): void
    {
        $config = [
            'glue' => ',',
            1 => '',
            2 => [
                'multiValue' => ['value1', 'value2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2'], $result);
    }

    /** @test */
    public function multipleMultiValuesWithGlue(): void
    {
        $config = [
            'glue' => ';',
            1 => [
                'multiValue' => ['value1', 'value2'],
            ],
            2 => [
                'multiValue' => ['value3', 'value4'],
            ],
        ];
        /** @var MultiValue $result */
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2;value3,value4', $result);
    }

    /** @test */
    public function firstNullSecondNullWithGlue(): void
    {
        $config = [
            'glue' => ',',
            1 => null,
            2 => null,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function firstNullSecondNotNullWithGlue(): void
    {
        $config = [
            'glue' => ',',
            1 => null,
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function firstNotNullSecondNullWithGlue(): void
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => null,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }
}

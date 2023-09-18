<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConcatenationValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConcatenationValueSource
 */
class ConcatenationValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'concatenation';

    /** @test */
    public function nonExistentFieldWillReturnNull(): void
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                'id1' => $this->createListItem(
                    $this->getValueConfiguration([
                        FieldValueSource::KEY_FIELD_NAME => 'field3',
                    ], 'field'),
                    'id1',
                    10
                ),
            ],
        ];
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertNull($output);
    }

    /** @test */
    public function emptyFieldWillReturnEmptyValueAndNotNull(): void
    {
        $this->data['field1'] = '';
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                'id1' => $this->createListItem(
                    $this->getValueConfiguration([
                        FieldValueSource::KEY_FIELD_NAME => 'field1',
                    ], 'field'),
                    'id1',
                    10
                ),
            ],
        ];
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertEquals('', $output);
    }

    /** @test */
    public function singleComlpexFieldWillBeReturnedAsIs(): void
    {
        $this->data['field1'] = new MultiValue(['foo', 'bar']);
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                'id1' => $this->createListItem(
                    $this->getValueConfiguration([
                        FieldValueSource::KEY_FIELD_NAME => 'field1',
                    ], 'field'),
                    'id1',
                    10
                ),
            ],
        ];
        /** @var MultiValueInterface $output */
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertInstanceOf(MultiValueInterface::class, $output);
        $this->assertEquals(['foo', 'bar'], $output->toArray());
    }

    /** @test */
    public function concatSimpleValues(): void
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                'id1' => $this->createListItem(
                    $this->getValueConfiguration([
                        FieldValueSource::KEY_FIELD_NAME => 'field1',
                    ], 'field'),
                    'id1',
                    10
                ),
                'id2' => $this->createListItem(
                    $this->getValueConfiguration([
                        FieldValueSource::KEY_FIELD_NAME => 'field2',
                    ], 'field'),
                    'id2',
                    20
                ),
            ],
        ];
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertEquals('value1 value2', $output);
    }

    /** @test */
    public function concatWithComplexValue(): void
    {
        $this->data['field1'] = new MultiValue(['value1.1', 'value1.2']);
        $this->data['field2'] = 'value2';
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                'id1' => $this->createListItem(
                    $this->getValueConfiguration([
                        FieldValueSource::KEY_FIELD_NAME => 'field1',
                    ], 'field'),
                    'id1',
                    10
                ),
                'id2' => $this->createListItem(
                    $this->getValueConfiguration([
                        FieldValueSource::KEY_FIELD_NAME => 'field2',
                    ], 'field'),
                    'id2',
                    20
                ),
            ],
        ];
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertEquals('value1.1,value1.2 value2', $output);
    }

    /** @test */
    public function customGlueIsUsed(): void
    {
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                'id1' => $this->createListItem(
                    $this->getValueConfiguration([
                        FieldValueSource::KEY_FIELD_NAME => 'field1',
                    ], 'field'),
                    'id1',
                    10
                ),
                'id2' => $this->createListItem(
                    $this->getValueConfiguration([
                        FieldValueSource::KEY_FIELD_NAME => 'field2',
                    ], 'field'),
                    'id2',
                    29
                ),
            ],
            ConcatenationValueSource::KEY_GLUE => '-',
        ];
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertEquals('value1-value2', $output);
    }
}

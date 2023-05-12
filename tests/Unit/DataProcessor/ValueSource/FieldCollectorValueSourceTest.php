<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldCollectorValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;

class FieldCollectorValueSourceTest extends ValueSourceTest
{
    use MultiValueTestTrait;

    protected const KEYWORD = 'fieldCollector';
    protected const CLASS_NAME = FieldCollectorValueSource::class;

    public function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $this->data['field3'] = 'value3';
    }

    protected function getNeutralConfig(): array
    {
        return [
            FieldCollectorValueSource::KEY_UNPROCESSED_ONLY => false,
            FieldCollectorValueSource::KEY_IGNORE_IF_EMPTY => false,
        ];
    }
    
    /** @test */
    public function noData(): void
    {
        $this->data = [];
        $output = $this->processValueSource($this->getNeutralConfig());
        $this->assertInstanceOf(MultiValueInterface::class, $output);
        $this->assertEquals('', (string) $output);
    }

    /** @test */
    public function collectWithDefaultConfig(): void
    {
        $output = $this->processValueSource($this->getNeutralConfig());
        $this->assertEquals("field1 = value1\nfield2 = value2\nfield3 = value3\n", (string) $output);
    }

    public function skipProcessedProvider(): array
    {
        return [
            // processed, useDefaultConfig, unprocessedOnly, expected
            [[],         true,  null,  "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            [['field2'], true,  null,  "field1 = value1\nfield3 = value3\n"],
            [['field2'], false, true,  "field1 = value1\nfield3 = value3\n"],
            [['field2'], false, false, "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
        ];
    }
    
    /**
     * @dataProvider skipProcessedProvider
     * @test
     */
    public function skipProcessed(array $processed, bool $useDefaultConfig, ?bool $unprocessedOnly, mixed $expected): void
    {
        foreach ($processed as $field) {
            $this->fieldTracker->markAsProcessed($field);
        }
        $config = $useDefaultConfig ? [] : $this->getNeutralConfig();
        if ($unprocessedOnly !== null) {
            $config[FieldCollectorValueSource::KEY_UNPROCESSED_ONLY] = $unprocessedOnly;
        }
        $output = $this->processValueSource($config);
        $this->assertEquals($expected, (string) $output);
    }
    
    public function ignoreIfEmptyProvider(): array
    {
        return [
            // value2, useDefaultConfig, ignoreIfEmpty, expected
            ['',   true,  null,  "field1 = value1\nfield3 = value3\n"],
            ['',   false, true,  "field1 = value1\nfield3 = value3\n"],
            ['',   false, false, "field1 = value1\nfield2 = \nfield3 = value3\n"],
            [null, false, true,  "field1 = value1\nfield3 = value3\n"],
        ];
    }

    /**
     * @dataProvider ignoreIfEmptyProvider
     * @test
     */
    public function ignoreIfEmpty(mixed $value2, bool $useDefaultConfig, ?bool $ignoreIfEmpty, mixed $expected): void
    {
        $this->data['field2'] = $value2;
        $config = $useDefaultConfig ? [] : $this->getNeutralConfig();
        if (!$useDefaultConfig) {
            if ($ignoreIfEmpty === null) {
                unset($config['ignoreIfEmpty']);
            } else {
                $config['ignoreIfEmpty'] = $ignoreIfEmpty;
            }
        }
        $output = $this->processValueSource($config);
        $this->assertEquals($expected, (string) $output);
    }

    public function templateProvider(): array
    {
        return [
            [null,                     "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            ['{key}\\s=\\s{value}\\n', "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            ['{key},{value};',         "field1,value1;field2,value2;field3,value3;"],
            ['{key}',                  "field1field2field3"],
            ['{value}',                "value1value2value3"],
        ];
    }

    /**
     * @dataProvider templateProvider
     * @test
     */
    public function template(?string $template, mixed $expected): void
    {
        $config = $this->getNeutralConfig();
        if ($template !== null) {
            $config[FieldCollectorValueSource::KEY_TEMPLATE] = $template;
        }
        $output = $this->processValueSource($config);
        $this->assertEquals($expected, (string) $output);
    }

    public function excludeProvider(): array
    {
        return [
            [null,            "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            ['field2',        "field1 = value1\nfield3 = value3\n"],
            ['field2,field3', "field1 = value1\n"],
        ];
    }

    /**
     * @dataProvider excludeProvider
     * @test
     */
    public function exclude(mixed $exclude, mixed $expected): void
    {
        $config = $this->getNeutralConfig();
        if ($exclude !== null) {
            $config[FieldCollectorValueSource::KEY_EXCLUDE] = $exclude;
        }
        $output = $this->processValueSource($config);
        $this->assertEquals($expected, (string) $output);
    }

    public function includeProvider(): array
    {
        return [
            'alreadyProcessedFieldsDoNotGetIncluddeWhenIncludeIsNotDefined' => [
                ['field1', 'field2'],
                true,
                null,
                "field3 = value3\n",
            ],
            'alreadyProcessedFieldsDoNotGetIncludedWhenIncludeEmpty' => [
                ['field1', 'field2'],
                true,
                '',
                "field3 = value3\n",
            ],
            'alreadyProcessedFieldsGetIncluded' => [
                ['field1', 'field2'],
                true,
                'field1',
                "field1 = value1\nfield3 = value3\n",
            ],
        ];
    }

    /**
     * @dataProvider includeProvider
     * @test
     */
    public function include(array $processed, bool $processedOnly, mixed $include, mixed $expected): void
    {
        foreach ($processed as $field) {
            $this->fieldTracker->markAsProcessed($field);
        }
        $config = $this->getNeutralConfig();
        $config[FieldCollectorValueSource::KEY_UNPROCESSED_ONLY] = $processedOnly;
        if ($include !== null) {
            $config[FieldCollectorValueSource::KEY_INCLUDE] = $include;
        }
        $output = $this->processValueSource($config);
        $this->assertEquals($expected, (string) $output);
    }

    /** @test */
    public function complexDataStructureKeptWhenTheTemplateIsTheValueItself(): void
    {
        $this->data = [
            'field1' => new MultiValue(['value1.1', 'value1.2']),
            'field2' => new MultiValue(['value2.1', 'value2.2']),
        ];
        $config = $this->getNeutralConfig();
        $config[FieldCollectorValueSource::KEY_TEMPLATE] = '{value}';

        /** @var MultiValueInterface */
        $output = $this->processValueSource($config);

        $this->assertMultiValue($output);

        $this->assertMultiValueEquals([
            new MultiValue(['value1.1', 'value1.2']),
            new MultiValue(['value2.1', 'value2.2']),
        ], $output);

        $this->assertEquals('value1.1,value1.2value2.1,value2.2', (string) $output);
    }
}
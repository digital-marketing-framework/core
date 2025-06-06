<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldCollectorValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends ValueSourceTestBase<FieldCollectorValueSource>
 */
class FieldCollectorValueSourceTest extends ValueSourceTestBase
{
    use MultiValueTestTrait;

    protected const KEYWORD = 'fieldCollector';

    protected const CLASS_NAME = FieldCollectorValueSource::class;

    protected const DEFAULT_CONFIG = [
        FieldCollectorValueSource::KEY_IGNORE_IF_EMPTY => FieldCollectorValueSource::DEFAULT_IGNORE_IF_EMPTY,
        FieldCollectorValueSource::KEY_UNPROCESSED_ONLY => FieldCollectorValueSource::DEFAULT_UNPROCESSED_ONLY,
        FieldCollectorValueSource::KEY_EXCLUDE => FieldCollectorValueSource::DEFAULT_EXCLUDE,
        FieldCollectorValueSource::KEY_INCLUDE => FieldCollectorValueSource::DEFAULT_INCLUDE,
        FieldCollectorValueSource::KEY_TEMPLATE => FieldCollectorValueSource::DEFAULT_TEMPLATE,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $this->data['field3'] = 'value3';
    }

    /**
     * @return array<string,mixed>
     */
    protected function getNeutralConfig(): array
    {
        return [
            FieldCollectorValueSource::KEY_UNPROCESSED_ONLY => false,
            FieldCollectorValueSource::KEY_IGNORE_IF_EMPTY => false,
        ];
    }

    #[Test]
    public function noData(): void
    {
        $this->data = [];
        $output = $this->processValueSource($this->getNeutralConfig());
        $this->assertInstanceOf(MultiValueInterface::class, $output);
        $this->assertEquals('', (string)$output);
    }

    #[Test]
    public function collectWithDefaultConfig(): void
    {
        $output = $this->processValueSource($this->getNeutralConfig());
        $this->assertEquals("field1 = value1\nfield2 = value2\nfield3 = value3\n", (string)$output);
    }

    /**
     * @return array<array{0:array<string>,1:bool,2:?bool,3:string}>
     */
    public static function skipProcessedProvider(): array
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
     * @param array<string> $processed
     */
    #[Test]
    #[DataProvider('skipProcessedProvider')]
    public function skipProcessed(array $processed, bool $useDefaultConfig, ?bool $unprocessedOnly, string $expected): void
    {
        foreach ($processed as $field) {
            $this->fieldTracker->markAsProcessed($field);
        }

        $config = $useDefaultConfig ? [] : $this->getNeutralConfig();
        if ($unprocessedOnly !== null) {
            $config[FieldCollectorValueSource::KEY_UNPROCESSED_ONLY] = $unprocessedOnly;
        }

        $output = $this->processValueSource($config);
        $this->assertEquals($expected, (string)$output);
    }

    /**
     * @return array<array{0:mixed,1:bool,2:?bool,3:string}>
     */
    public static function ignoreIfEmptyProvider(): array
    {
        return [
            // value2, useDefaultConfig, ignoreIfEmpty, expected
            ['',   true,  null,  "field1 = value1\nfield3 = value3\n"],
            ['',   false, true,  "field1 = value1\nfield3 = value3\n"],
            ['',   false, false, "field1 = value1\nfield2 = \nfield3 = value3\n"],
            [null, false, true,  "field1 = value1\nfield3 = value3\n"],
        ];
    }

    #[Test]
    #[DataProvider('ignoreIfEmptyProvider')]
    public function ignoreIfEmpty(mixed $value2, bool $useDefaultConfig, ?bool $ignoreIfEmpty, string $expected): void
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
        $this->assertEquals($expected, (string)$output);
    }

    /**
     * @return array<array{0:?string,1:string}>
     */
    public static function templateProvider(): array
    {
        return [
            [null,                     "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            ['{key}\\s=\\s{value}\\n', "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            ['{key},{value};',         'field1,value1;field2,value2;field3,value3;'],
            ['{key}',                  'field1field2field3'],
            ['{value}',                'value1value2value3'],
        ];
    }

    #[Test]
    #[DataProvider('templateProvider')]
    public function template(?string $template, string $expected): void
    {
        $config = $this->getNeutralConfig();
        if ($template !== null) {
            $config[FieldCollectorValueSource::KEY_TEMPLATE] = $template;
        }

        $output = $this->processValueSource($config);
        $this->assertEquals($expected, (string)$output);
    }

    /**
     * @return array<array{0:?string,1:string}>
     */
    public static function excludeProvider(): array
    {
        return [
            [null,            "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            ['field2',        "field1 = value1\nfield3 = value3\n"],
            ['field2,field3', "field1 = value1\n"],
        ];
    }

    #[Test]
    #[DataProvider('excludeProvider')]
    public function exclude(?string $exclude, string $expected): void
    {
        $config = $this->getNeutralConfig();
        if ($exclude !== null) {
            $config[FieldCollectorValueSource::KEY_EXCLUDE] = $exclude;
        }

        $output = $this->processValueSource($config);
        $this->assertEquals($expected, (string)$output);
    }

    /**
     * @return array<array{0:array<string>,1:bool,2:?string,3:string}>
     */
    public static function includeProvider(): array
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
     * @param array<string> $processed
     */
    #[Test]
    #[DataProvider('includeProvider')]
    public function include(array $processed, bool $processedOnly, ?string $include, string $expected): void
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
        $this->assertEquals($expected, (string)$output);
    }

    #[Test]
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

        $this->assertEquals('value1.1,value1.2value2.1,value2.2', (string)$output);
    }
}

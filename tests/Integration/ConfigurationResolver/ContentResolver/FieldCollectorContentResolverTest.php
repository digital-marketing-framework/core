<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\FieldCollectorContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;

/**
 * @covers FieldCollectorContentResolver
 */
class FieldCollectorContentResolverTest extends AbstractContentResolverTest
{
    use MultiValueTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $this->data['field3'] = 'value3';
    }

    protected function getNeutralConfig(): array
    {
        return [
            'fieldCollector' => [
                'unprocessedOnly' => false,
                'ignoreIfEmpty' => false,
            ],
        ];
    }

    /** @test */
    public function noData(): void
    {
        $this->data = [];
        $config = [
            'fieldCollector' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function collectWithDefaultConfig(): void
    {
        $config = [
            'fieldCollector' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("field1 = value1\nfield2 = value2\nfield3 = value3\n", (string)$result);
    }

    public function skipProcessedProvider(): array
    {
        return [
            // processed, useDefaultConfig, unprocessedOnly, expected
            [[],         true,  null,  "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            [['field2'], true,  null,  "field1 = value1\nfield3 = value3\n"],
            [['field2'], false, true,  "field1 = value1\nfield3 = value3\n"],
            [['field2'], false, false, "field1 = value1\nfield2 = value2\nfield3 = value3\n"],

            // TODO: the evaluations for this resolver should probably not count for the unprocessedOnly option, do they?
            //       currently field1 is excluded because the unprocessedOnly evaluation processed it
            //[['field2'], false, ['field1' => 'value1'], "field1 = value1\nfield3 = value3\n"],
            [['field2'], false, ['field1' => 'value2'], "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
        ];
    }

    /**
     * @dataProvider skipProcessedProvider
     * @test
     */
    public function skipProcessed(array $processed, bool $useDefaultConfig, mixed $unprocessedOnly, mixed $expected): void
    {
        foreach ($processed as $field) {
            $this->fieldTracker->markAsProcessed($field);
        }
        $config = $useDefaultConfig ? ['fieldCollector' => true] : $this->getNeutralConfig();
        if ($unprocessedOnly !== null) {
            $config['fieldCollector']['unprocessedOnly'] = $unprocessedOnly;
        }
        $result = $this->runResolverProcess($config);
        $this->assertEquals($expected, (string)$result);
    }

    public function ignoreIfEmptyProvider(): array
    {
        return [
            // value2, useDefaultConfig, ignoreIfEmpty, expected
            ['',   true,  null,  "field1 = value1\nfield3 = value3\n"],
            ['',   false, true,  "field1 = value1\nfield3 = value3\n"],
            ['',   false, false, "field1 = value1\nfield2 = \nfield3 = value3\n"],
            [null, false, true,  "field1 = value1\nfield3 = value3\n"],

            ['',   false, ['field1' => 'value1'], "field1 = value1\nfield3 = value3\n"],
            ['',   false, ['field1' => 'value2'], "field1 = value1\nfield2 = \nfield3 = value3\n"],
        ];
    }

    /**
     * @dataProvider ignoreIfEmptyProvider
     * @test
     */
    public function ignoreIfEmpty(mixed $value2, bool $useDefaultConfig, mixed $ignoreIfEmpty, mixed $expected): void
    {
        $this->data['field2'] = $value2;
        $config = $useDefaultConfig ? ['fieldCollector' => true] : $this->getNeutralConfig();
        if (!$useDefaultConfig) {
            if ($ignoreIfEmpty === null) {
                unset($config['fieldCollector']['ignoreIfEmpty']);
            } else {
                $config['fieldCollector']['ignoreIfEmpty'] = $ignoreIfEmpty;
            }
        }
        $result = $this->runResolverProcess($config);
        $this->assertEquals($expected, (string)$result);
    }

    public function templateProvider(): array
    {
        return [
            [true,                                  "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            [['template' => '{key}\s=\s{value}\n'], "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            [['template' => '{key}'],               "field1field2field3"],
            [['template' => '{value}'],             "value1value2value3"],

            [
                [
                    'unprocessedOnly' => false,
                    'template' => [
                        'glue' => ',',
                        1 => '{key}',
                        2 => '{value};',
                    ],
                ],
                "field1,value1;field2,value2;field3,value3;"
            ],
        ];
    }

    /**
     * @dataProvider templateProvider
     * @test
     */
    public function template(mixed $config, mixed $expected): void
    {
        $config = [
            'fieldCollector' => $config,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals($expected, (string)$result);
    }

    public function excludeProvider(): array
    {
        return [
            [null,            "field1 = value1\nfield2 = value2\nfield3 = value3\n"],
            ['field2',        "field1 = value1\nfield3 = value3\n"],
            ['field2,field3', "field1 = value1\n"],

            [
                ['multiValue' => ['field2', 'field3']],
                "field1 = value1\n"
            ]
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
            $config['fieldCollector']['exclude'] = $exclude;
        }
        $result = $this->runResolverProcess($config);
        $this->assertEquals($expected, (string)$result);
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
            'alreadyProcessedFieldsDoNotGetIncluddeWhenIncludeIsDisabled' => [
                ['field1', 'field2'],
                true,
                false,
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
        $config['fieldCollector']['unprocessedOnly'] = $processedOnly;
        if ($include !== null) {
            $config['fieldCollector']['include'] = $include;
        }
        $result = $this->runResolverProcess($config);
        $this->assertEquals($expected, (string)$result);
    }

    /**
     * @test
     */
    public function complexDataStructureKeptWhenTheTemplateIsTheValueItself(): void
    {
        $this->data = [
            'field1' => new MultiValue(['value1.1', 'value1.2']),
            'field2' => new MultiValue(['value2.1', 'value2.2']),
        ];
        $config = $this->getNeutralConfig();
        $config['fieldCollector']['template'] = '{value}';
        /** @var MultiValueInterface */
        $result = $this->runResolverProcess($config);

        $this->assertMultiValue($result);

        $this->assertMultiValueEquals([
            new MultiValue(['value1.1', 'value1.2']),
            new MultiValue(['value2.1', 'value2.2']),
        ], $result);
    }
}

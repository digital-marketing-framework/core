<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class PassthroughFieldsDataMapperTest extends DataMapperTestBase
{
    protected const CLASS_NAME = PassthroughFieldsDataMapper::class;

    protected const KEYWORD = 'passthroughFields';

    protected const DEFAULT_CONFIG = [
        PassthroughFieldsDataMapper::KEY_ENABLED => PassthroughFieldsDataMapper::DEFAULT_ENABLED,
        PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => false,
        PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => '',
    ];

    /**
     * @return array<array{
     *  0:array<string,string|ValueInterface|null>,
     *  1:array<string,string|ValueInterface|null>,
     *  2?:?array<string,mixed>,
     *  3?:array<string>,
     *  4?:array<string,string|ValueInterface|null>
     * }>
     */
    public static function mapDataDataProvider(): array
    {
        return [
            [
                [],
                [],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => false,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => '',
                ],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => false,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => '',
                ],
            ],
            [
                [],
                [],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => false,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => false,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => '',
                ],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                [],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => false,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => false,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => '',
                ],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1'],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => true,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => '',
                ],
                ['field2'],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => true,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => 'field2',
                ],
                ['field2'],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1'],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => true,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => 'field1',
                ],
                ['field2'],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
                ['field1' => 'value1', 'field2' => 'value2'],
                [
                    PassthroughFieldsDataMapper::KEY_ENABLED => true,
                    PassthroughFieldsDataMapper::KEY_UNPROCESSED_ONLY => true,
                    PassthroughFieldsDataMapper::KEY_INCLUDE_FIELDS => 'field1,field2',
                ],
                ['field2'],
            ],
        ];
    }

    /**
     * @param array<string,string|ValueInterface|null> $inputData
     * @param array<string,string|ValueInterface|null> $expectedOutputData
     * @param ?array<string,mixed> $config
     * @param ?array<string> $processedFields
     * @param array<string,string|ValueInterface|null> $target
     */
    #[Test]
    #[DataProvider('mapDataDataProvider')]
    public function mapDataTest(array $inputData, array $expectedOutputData, ?array $config = null, ?array $processedFields = null, ?array $target = null): void
    {
        if ($processedFields !== null) {
            foreach ($processedFields as $field) {
                $this->fieldTracker->markAsProcessed($field);
            }
        }

        $this->mapData($inputData, $expectedOutputData, $config, $target);
    }
}

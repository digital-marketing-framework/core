<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConcatenationValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends ValueSourceTestBase<ConcatenationValueSource>
 */
class ConcatenationValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'concatenation';

    protected const CLASS_NAME = ConcatenationValueSource::class;

    protected const DEFAULT_CONFIG = [
        ConcatenationValueSource::KEY_GLUE => ConcatenationValueSource::DEFAULT_GLUE,
        ConcatenationValueSource::KEY_SKIP_EMPTY_VALUES => ConcatenationValueSource::DEFAULT_SKIP_EMPTY_VALUES,
        ConcatenationValueSource::KEY_VALUES => [],
    ];

    #[Test]
    public function emptyConfigurationLeadsToNullValue(): void
    {
        $output = $this->processValueSource([]);
        $this->assertNull($output);
    }

    #[Test]
    public function nonExistentFieldWillReturnNull(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
            ],
        ];
        $this->dataProcessor->method('processValue')->with($subConfig1)->willReturn(null);
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }

    #[Test]
    public function emptyFieldWillReturnEmptyValueAndNotNull(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
            ],
        ];
        $this->dataProcessor->method('processValue')->with($subConfig1)->willReturn('');
        $output = $this->processValueSource($config);
        $this->assertEquals('', $output);
    }

    #[Test]
    public function singleComlpexFieldWillBeReturnedAsIs(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
            ],
        ];
        $this->dataProcessor->method('processValue')->with($subConfig1)->willReturn(new MultiValue(['foo', 'bar']));
        /** @var MultiValueInterface $output */
        $output = $this->processValueSource($config);
        $this->assertInstanceOf(MultiValueInterface::class, $output);
        $this->assertEquals(['foo', 'bar'], $output->toArray());
    }

    #[Test]
    public function concatSimpleValues(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
            ],
        ];
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', [
            [$subConfig1],
            [$subConfig2],
        ], [
            'value1',
            'value2',
        ]);
        $output = $this->processValueSource($config);
        $this->assertEquals('value1 value2', $output);
    }

    #[Test]
    public function concatWithComplexValue(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
            ],
        ];
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', [
            [$subConfig1],
            [$subConfig2],
        ], [
            new MultiValue(['value1.1', 'value1.2']),
            'value2',
        ]);
        $output = $this->processValueSource($config);
        $this->assertEquals('value1.1,value1.2 value2', $output);
    }

    #[Test]
    public function customGlueIsUsed(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
            ],
            ConcatenationValueSource::KEY_GLUE => '-',
        ];
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', [
            [$subConfig1],
            [$subConfig2],
        ], [
            'value1',
            'value2',
        ]);
        $output = $this->processValueSource($config);
        $this->assertEquals('value1-value2', $output);
    }

    #[Test]
    public function emptyFieldWillReturnNullIfEmptyValuesAreSkipped(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
            ],
            ConcatenationValueSource::KEY_SKIP_EMPTY_VALUES => true,
        ];
        $this->dataProcessor->method('processValue')->with($subConfig1)->willReturn('');
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }

    #[Test]
    public function emptyValuesAreConcatenatedByDefault(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $subConfig3 = ['configKey3' => 'configValue3'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
                $this->createListItem($subConfig3, 'id3', 30),
            ],
        ];
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', [
            [$subConfig1],
            [$subConfig2],
            [$subConfig3],
        ], [
            'value1',
            '',
            'value3',
        ]);
        $output = $this->processValueSource($config);
        $this->assertEquals('value1  value3', $output);
    }

    #[Test]
    public function emptyValuesAreSkipped(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $subConfig3 = ['configKey3' => 'configValue3'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
                $this->createListItem($subConfig3, 'id3', 30),
            ],
            ConcatenationValueSource::KEY_SKIP_EMPTY_VALUES => true,
        ];
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', [
            [$subConfig1],
            [$subConfig2],
            [$subConfig3],
        ], [
            'value1',
            '',
            'value3',
        ]);
        $output = $this->processValueSource($config);
        $this->assertEquals('value1 value3', $output);
    }

    #[Test]
    public function onlyEmptyValuesWillReturnNullIfEmptyValuesAreSkipped(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
            ],
            ConcatenationValueSource::KEY_SKIP_EMPTY_VALUES => true,
        ];
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', [
            [$subConfig1],
            [$subConfig2],
        ], [
            '',
            '',
        ]);
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }

    #[Test]
    public function lastRemainingValueWillBeReturnedAsIsIfEmptyValuesAreSkipped(): void
    {
        $subConfig1 = ['configKey1' => 'configValue1'];
        $subConfig2 = ['configKey2' => 'configValue2'];
        $config = [
            ConcatenationValueSource::KEY_VALUES => [
                $this->createListItem($subConfig1, 'id1', 10),
                $this->createListItem($subConfig2, 'id2', 20),
            ],
            ConcatenationValueSource::KEY_SKIP_EMPTY_VALUES => true,
        ];
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', [
            [$subConfig1],
            [$subConfig2],
        ], [
            '',
            new MultiValue(['value2.1', 'value2.2']),
        ]);
        /** @var MultiValueInterface $output */
        $output = $this->processValueSource($config);
        $this->assertInstanceOf(MultiValueInterface::class, $output);
        $this->assertEquals(['value2.1', 'value2.2'], $output->toArray());
    }
}

<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConditionValueSource;

class ConditionValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'condition';

    protected const CLASS_NAME = ConditionValueSource::class;

    /** @test */
    public function emptyConfigurationThrowsException(): void
    {
        $this->expectExceptionMessage('Condition value source - no condition given.');
        $this->processValueSource([]);
    }

    /**
     * @return array<array{0:mixed,1:bool,2:mixed,3:mixed,4:array<string,mixed>,5:?array<string,mixed>,6:?array<string,mixed>}>
     */
    public function conditionValueSourceDataProvider(): array
    {
        return [
            [
                'value1',
                true,
                'value1',
                'value2',
                ['ifConfigKey' => 'ifConfigValue'],
                ['thenConfigKey' => 'thenConfigValue'],
                ['elseConfigKey' => 'elseConfigValue'],
            ],
            [
                null,
                true,
                null,
                'value2',
                ['ifConfigKey' => 'ifConfigValue'],
                null,
                ['elseConfigKey' => 'elseConfigValue'],
            ],
            [
                null,
                true,
                null,
                null,
                ['ifConfigKey' => 'ifConfigValue'],
                null,
                null,
            ],
            [
                'value2',
                false,
                'value1',
                'value2',
                ['ifConfigKey' => 'ifConfigValue'],
                ['thenConfigKey' => 'thenConfigValue'],
                ['elseConfigKey' => 'elseConfigValue'],
            ],
            [
                null,
                false,
                'value1',
                null,
                ['ifConfigKey' => 'ifConfigValue'],
                ['thenConfigKey' => 'thenConfigValue'],
                null,
            ],
            [
                null,
                false,
                null,
                null,
                ['ifConfigKey' => 'ifConfigValue'],
                null,
                null,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $ifConfig
     * @param ?array<string,mixed> $thenConfig
     * @param ?array<string,mixed> $elseConfig
     *
     * @test
     *
     * @dataProvider conditionValueSourceDataProvider
     */
    public function conditionValueSource(
        mixed $expectedResult,
        bool $evalResult,
        mixed $thenResult,
        mixed $elseResult,
        array $ifConfig = [],
        ?array $thenConfig = null,
        ?array $elseConfig = null
    ): void {
        $valueWith = [];
        $valueResults = [];
        if ($thenConfig !== null) {
            $valueWith[] = [$thenConfig];
            $valueResults[] = $thenResult;
        }

        if ($elseConfig !== null) {
            $valueWith[] = [$elseConfig];
            $valueResults[] = $elseResult;
        }

        $this->dataProcessor->method('processValue')->withConsecutive(...$valueWith)->willReturnOnConsecutiveCalls(...$valueResults);
        $this->dataProcessor->method('processEvaluation')->with($ifConfig)->willReturn($evalResult);

        $config = [
            ConditionValueSource::KEY_IF => $ifConfig,
            ConditionValueSource::KEY_THEN => $thenConfig,
            ConditionValueSource::KEY_ELSE => $elseConfig,
        ];
        $output = $this->processValueSource($config);
        $this->assertEquals($expectedResult, $output);
    }
}

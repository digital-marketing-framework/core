<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\EnvironmentVariableValueSource;
use DigitalMarketingFramework\Core\Environment\EnvironmentServiceInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @extends ValueSourceTestBase<EnvironmentVariableValueSource>
 */
class EnvironmentVariableValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'environmentVariable';

    protected const CLASS_NAME = EnvironmentVariableValueSource::class;

    protected EnvironmentServiceInterface&MockObject $environmentService;

    /** @var array<string,string> */
    protected array $environment = [
        'SF_OID' => '00D000000000001',
        'EMPTY_VARIABLE' => '',
        'SECRET_TOKEN' => 'do-not-leak',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->coreSettings->method('getAllowedEnvironmentVariables')->willReturn(['SF_OID', 'EMPTY_VARIABLE', 'UNSET_VARIABLE']);

        $this->environmentService = $this->createMock(EnvironmentServiceInterface::class);
        $this->environmentService->method('environmentVariableExists')->willReturnCallback(fn (string $name): bool => array_key_exists($name, $this->environment));
        $this->environmentService->method('getEnvironmentVariable')->willReturnCallback(fn (string $name): string => $this->environment[$name] ?? '');
    }

    protected function processObjectAwareness(): void
    {
        parent::processObjectAwareness();
        $this->subject->setEnvironmentService($this->environmentService);
    }

    /**
     * @return array<string,array{string,?string}>
     */
    public static function environmentVariableDataProvider(): array
    {
        return [
            'allowedAndSet' => ['SF_OID', '00D000000000001'],
            'allowedAndSetButEmpty' => ['EMPTY_VARIABLE', ''],
            'allowedButNotSet' => ['UNSET_VARIABLE', null],
            'noNameGiven' => ['', null],
        ];
    }

    #[Test]
    #[DataProvider('environmentVariableDataProvider')]
    public function environmentVariable(string $name, ?string $expected): void
    {
        $this->logger->expects($this->never())->method('warning');

        $result = $this->processValueSource([
            EnvironmentVariableValueSource::KEY_VARIABLE_NAME => $name,
        ]);

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function variableThatIsNotAllowedIsNotReadAndLogsAWarning(): void
    {
        $this->environmentService->expects($this->never())->method('getEnvironmentVariable');
        $this->logger->expects($this->once())->method('warning')->with($this->stringContains('SECRET_TOKEN'));

        $result = $this->processValueSource([
            EnvironmentVariableValueSource::KEY_VARIABLE_NAME => 'SECRET_TOKEN',
        ]);

        $this->assertNull($result);
    }

    #[Test]
    public function allowListIsCaseSensitive(): void
    {
        $this->environment['sf_oid'] = 'lower-case';
        $this->logger->expects($this->once())->method('warning');

        $result = $this->processValueSource([
            EnvironmentVariableValueSource::KEY_VARIABLE_NAME => 'sf_oid',
        ]);

        $this->assertNull($result);
    }
}

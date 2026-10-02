<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\EnvironmentVariableValueSource;
use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(EnvironmentVariableValueSource::class)]
class EnvironmentVariableValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'environmentVariable';

    /** @var array<string,string> */
    protected const ENVIRONMENT = [
        'ANYREL_TEST_ALLOWED' => 'allowed-value',
        'ANYREL_TEST_ALLOWED_EMPTY' => '',
        'ANYREL_TEST_NOT_ALLOWED' => 'secret-value',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (static::ENVIRONMENT as $name => $value) {
            putenv($name . '=' . $value);
        }

        $this->registry->getGlobalConfiguration()->set('core', [
            CoreGlobalConfigurationSchema::KEY_ALLOWED_ENVIRONMENT_VARIABLES => 'ANYREL_TEST_ALLOWED, ANYREL_TEST_ALLOWED_EMPTY, ANYREL_TEST_ALLOWED_UNSET',
        ]);
    }

    protected function tearDown(): void
    {
        foreach (array_keys(static::ENVIRONMENT) as $name) {
            putenv($name);
        }

        parent::tearDown();
    }

    /**
     * @return array<string,array{string,?string}>
     */
    public static function environmentVariableDataProvider(): array
    {
        return [
            'allowedAndSet' => ['ANYREL_TEST_ALLOWED', 'allowed-value'],
            'allowedAndSetButEmpty' => ['ANYREL_TEST_ALLOWED_EMPTY', ''],
            'allowedButNotSet' => ['ANYREL_TEST_ALLOWED_UNSET', null],
            'notAllowed' => ['ANYREL_TEST_NOT_ALLOWED', null],
            'noNameGiven' => ['', null],
        ];
    }

    #[Test]
    #[DataProvider('environmentVariableDataProvider')]
    public function environmentVariableValue(string $name, ?string $expectedResult): void
    {
        $output = $this->processValueSource($this->getValueSourceConfiguration([
            EnvironmentVariableValueSource::KEY_VARIABLE_NAME => $name,
        ]));

        $this->assertSame($expectedResult, $output);
    }

    #[Test]
    public function allowedVariablesAreSuggestedInTheSchemaDocument(): void
    {
        $valueSet = $this->registry->getConfigurationSchemaDocument()->getValueSet(EnvironmentVariableValueSource::VALUE_SET_ALLOWED_VARIABLES);

        $this->assertNotNull($valueSet);
        $this->assertSame(
            [
                'ANYREL_TEST_ALLOWED' => 'ANYREL_TEST_ALLOWED',
                'ANYREL_TEST_ALLOWED_EMPTY' => 'ANYREL_TEST_ALLOWED_EMPTY',
                'ANYREL_TEST_ALLOWED_UNSET' => 'ANYREL_TEST_ALLOWED_UNSET',
            ],
            $valueSet->toArray()
        );
    }
}

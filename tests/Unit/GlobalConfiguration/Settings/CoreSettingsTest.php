<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\CoreSettings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CoreSettings::class)]
class CoreSettingsTest extends TestCase
{
    /**
     * @return array<string,array{?string,array<string>}>
     */
    public static function allowedEnvironmentVariablesDataProvider(): array
    {
        return [
            'notConfigured' => [null, []],
            'empty' => ['', []],
            'single' => ['SF_OID', ['SF_OID']],
            'multiple' => ['SF_OID,SF_ORG_ID', ['SF_OID', 'SF_ORG_ID']],
            'whitespaceIsTrimmed' => [' SF_OID , SF_ORG_ID ', ['SF_OID', 'SF_ORG_ID']],
            'emptyEntriesAreDropped' => [',SF_OID,, ,SF_ORG_ID,', ['SF_OID', 'SF_ORG_ID']],
        ];
    }

    /**
     * @param array<string> $expected
     */
    #[Test]
    #[DataProvider('allowedEnvironmentVariablesDataProvider')]
    public function allowedEnvironmentVariables(?string $value, array $expected): void
    {
        $settings = new CoreSettings();
        $settings->injectSettings($value === null ? [] : [CoreGlobalConfigurationSchema::KEY_ALLOWED_ENVIRONMENT_VARIABLES => $value]);

        $this->assertSame($expected, $settings->getAllowedEnvironmentVariables());
    }
}

<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\GlobalConfiguration;

use DigitalMarketingFramework\Core\GlobalConfiguration\DefaultGlobalConfiguration;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Tests\GlobalConfiguration\Schema\GenericGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\Tests\GlobalConfiguration\Settings\GenericGlobalSettings;
use DigitalMarketingFramework\Core\Tests\Integration\CoreRegistryTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DigitalMarketingFramework\Core\GlobalConfiguration\DefaultGlobalConfiguration
 */
class GlobalSettingsTest extends TestCase
{
    use CoreRegistryTestTrait;

    protected GlobalConfigurationInterface $globalConfiguration;

    protected function setUp(): void
    {
        $this->initRegistry();

        $this->globalConfiguration = new DefaultGlobalConfiguration($this->registry);
        $this->registry->setGlobalConfiguration($this->globalConfiguration);

        parent::setUp();
    }

    protected function createGlobalConfigurationSchema(string $packageName): GenericGlobalConfigurationSchema
    {
        $schema = new GenericGlobalConfigurationSchema();
        $this->registry->addGlobalConfigurationSchemaForPackage($packageName, $schema);

        return $schema;
    }

    /** @test */
    public function unknownPackageResultsInEmptyArray(): void
    {
        $settings = $this->registry->getGlobalConfiguration()->getGlobalSettings(GenericGlobalSettings::class, 'packageKey1');
        $config = $settings->getInjectedSettings();

        static::assertEquals([], $config);
    }

    /** @test */
    public function knownPackageWithoutSchemaReturnsConfig(): void
    {
        $config = ['a' => 'A'];
        $this->globalConfiguration->set('packageKey1', $config);

        $settings = $this->registry->getGlobalConfiguration()->getGlobalSettings(GenericGlobalSettings::class, 'packageKey1');
        $config = $settings->getInjectedSettings();

        static::assertEquals(['a' => 'A'], $config);
    }

    /** @test */
    public function knownPackageWithSchemaReturnsConfig(): void
    {
        $schema = $this->createGlobalConfigurationSchema('packageKey1');
        $schema->addProperty('a', new StringSchema('A'));

        $config = ['a' => 'A2'];
        $this->globalConfiguration->set('packageKey1', $config);

        $settings = $this->registry->getGlobalConfiguration()->getGlobalSettings(GenericGlobalSettings::class, 'packageKey1');
        $config = $settings->getInjectedSettings();

        static::assertEquals(['a' => 'A2'], $config);
    }

    /** @test */
    public function knownPackageWithSchemaReturnsMixedConfig(): void
    {
        $schema = $this->createGlobalConfigurationSchema('packageKey1');
        $schema->addProperty('a', new StringSchema('A'));
        $schema->addProperty('b', new StringSchema('B'));

        $config = ['a' => 'A2'];
        $this->globalConfiguration->set('packageKey1', $config);

        $settings = $this->registry->getGlobalConfiguration()->getGlobalSettings(GenericGlobalSettings::class, 'packageKey1');
        $config = $settings->getInjectedSettings();

        static::assertEquals(['a' => 'A2', 'b' => 'B'], $config);
    }

    /** @test */
    public function componentUnknownPackageResultsInEmptyArray(): void
    {
        $settings = $this->registry->getGlobalConfiguration()->getGlobalSettings(
            GenericGlobalSettings::class,
            'packageKey1',
            'component1'
        );
        $config = $settings->getInjectedSettings();

        static::assertEquals([], $config);
    }

    /** @test */
    public function componentKnownPackageWithoutSchemaReturnsConfig(): void
    {
        $config = ['component1' => ['b' => 'B']];
        $this->globalConfiguration->set('packageKey1', $config);

        $settings = $this->registry->getGlobalConfiguration()->getGlobalSettings(
            GenericGlobalSettings::class,
            'packageKey1',
            'component1'
        );
        $config = $settings->getInjectedSettings();

        static::assertEquals(['b' => 'B'], $config);
    }

    /** @test */
    public function componentKnownPackageWithSchemaReturnsConfig(): void
    {
        $schema = $this->createGlobalConfigurationSchema('packageKey1');
        $component1Schema = new ContainerSchema();
        $component1Schema->addProperty('b', new StringSchema('B'));

        $schema->addProperty('component1', $component1Schema);

        $config = ['component1' => ['b' => 'B2']];
        $this->globalConfiguration->set('packageKey1', $config);

        $settings = $this->registry->getGlobalConfiguration()->getGlobalSettings(
            GenericGlobalSettings::class,
            'packageKey1',
            'component1'
        );
        $config = $settings->getInjectedSettings();

        static::assertEquals(['b' => 'B2'], $config);
    }

    /** @test */
    public function componentKnownPackageWithSchemaReturnsMixedConfig(): void
    {
        $schema = $this->createGlobalConfigurationSchema('packageKey1');

        $component1Schema = new ContainerSchema();
        $component1Schema->addProperty('b', new StringSchema('B'));
        $component1Schema->addProperty('c', new StringSchema('C'));

        $schema->addProperty('component1', $component1Schema);

        $config = ['component1' => ['b' => 'B2']];
        $this->globalConfiguration->set('packageKey1', $config);

        $settings = $this->registry->getGlobalConfiguration()->getGlobalSettings(
            GenericGlobalSettings::class,
            'packageKey1',
            'component1'
        );
        $config = $settings->getInjectedSettings();

        static::assertEquals(['b' => 'B2', 'c' => 'C'], $config);
    }
}

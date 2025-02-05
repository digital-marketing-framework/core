<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\GlobalConfiguration;

use DigitalMarketingFramework\Core\GlobalConfiguration\DefaultGlobalConfiguration;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Tests\GlobalConfiguration\Schema\GenericGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\Tests\Integration\CoreRegistryTestTrait;
use DigitalMarketingFramework\Core\Tests\Service\GloballyConfigurableObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DigitalMarketingFramework\Core\Registry\Registry
 */
class GloballyConfigurableTest extends TestCase
{
    use CoreRegistryTestTrait;

    protected GlobalConfigurationInterface $globalConfiguration;

    protected function setUp(): void
    {
        $this->initRegistry();

        $this->globalConfiguration = new DefaultGlobalConfiguration();
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
        $service = $this->registry->createObject(GloballyConfigurableObject::class, ['packageKey1']);
        $config = $service->getGloballyConfiguredData();

        static::assertEquals([], $config);
    }

    /** @test */
    public function knownPackageWithoutSchemaReturnsConfig(): void
    {
        $config = ['a' => 'A'];
        $this->globalConfiguration->set('packageKey1', $config);

        $service = $this->registry->createObject(GloballyConfigurableObject::class, ['packageKey1']);
        $config = $service->getGloballyConfiguredData();

        static::assertEquals(['a' => 'A'], $config);
    }

    /** @test */
    public function knownPackageWithSchemaReturnsConfig(): void
    {
        $schema = $this->createGlobalConfigurationSchema('packageKey1');
        $schema->addProperty('a', new StringSchema('A'));

        $config = ['a' => 'A2'];
        $this->globalConfiguration->set('packageKey1', $config);

        $service = $this->registry->createObject(GloballyConfigurableObject::class, ['packageKey1']);
        $config = $service->getGloballyConfiguredData();

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

        $service = $this->registry->createObject(GloballyConfigurableObject::class, ['packageKey1']);
        $config = $service->getGloballyConfiguredData();

        static::assertEquals(['a' => 'A2', 'b' => 'B'], $config);
    }

    /** @test */
    public function componentUnknownPackageResultsInDefaultNull(): void
    {
        $service = $this->registry->createObject(GloballyConfigurableObject::class, ['packageKey1']);
        $config = $service->testGlobalConfig('key1', 'componentKey1');

        static::assertNull($config);
    }

    /** @test */
    public function componentUnknownPackageResultsInDefault(): void
    {
        $service = $this->registry->createObject(GloballyConfigurableObject::class, ['packageKey1']);
        $config = $service->testGlobalConfig('key1', 'componentKey1', 'value1');

        static::assertEquals('value1', $config);
    }

    /** @test */
    public function componentKnownPackageWithoutSchemaReturnsConfig(): void
    {
        $config = ['a' => ['b' => 'B']];
        $this->globalConfiguration->set('packageKey1', $config);

        $service = $this->registry->createObject(GloballyConfigurableObject::class, ['packageKey1']);
        $config = $service->testGlobalConfig('b', 'a');

        static::assertEquals('B', $config);
    }

    /** @test */
    public function componentKnownPackageWithSchemaReturnsConfig(): void
    {
        $schema = $this->createGlobalConfigurationSchema('packageKey1');
        $aSchema = new ContainerSchema();
        $aSchema->addProperty('b', new StringSchema('B'));

        $schema->addProperty('a', $aSchema);

        $config = ['a' => ['b' => 'B2']];
        $this->globalConfiguration->set('packageKey1', $config);

        $service = $this->registry->createObject(GloballyConfigurableObject::class, ['packageKey1']);
        $config = $service->testGlobalConfig('b', 'a');

        static::assertEquals('B2', $config);
    }

    /** @test */
    public function componentKnownPackageWithSchemaReturnsMixedConfig(): void
    {
        $schema = $this->createGlobalConfigurationSchema('packageKey1');

        $bSchema = new ContainerSchema();
        $bSchema->addProperty('c', new StringSchema('C'));
        $bSchema->addProperty('d', new StringSchema('D'));

        $aSchema = new ContainerSchema();
        $aSchema->addProperty('b', $bSchema);

        $schema->addProperty('a', $aSchema);

        $config = ['a' => ['b' => ['c' => 'C2']]];
        $this->globalConfiguration->set('packageKey1', $config);

        $service = $this->registry->createObject(GloballyConfigurableObject::class, ['packageKey1']);
        $config = $service->testGlobalConfig('b', 'a');

        static::assertEquals(['c' => 'C2', 'd' => 'D'], $config);
    }
}

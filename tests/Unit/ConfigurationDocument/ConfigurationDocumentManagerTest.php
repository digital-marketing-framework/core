<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManager;
use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentIncludeLoopException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigurationDocumentManagerTest extends TestCase
{
    protected ConfigurationDocumentStorageInterface&MockObject $staticStorage;

    protected ConfigurationDocumentStorageInterface&MockObject $storage;

    protected ConfigurationDocumentParserInterface&MockObject $parser;

    protected const SYS_DEFAULTS_CONFIGURATION = ['sys_config_key' => 'sys_config_value'];

    /** @var array<array{identifier:string,document:string,configuration:array<mixed>}> */
    protected array $documents = [];

    protected ConfigurationDocumentManager $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staticStorage = $this->createMock(ConfigurationDocumentStorageInterface::class);
        $this->storage = $this->createMock(ConfigurationDocumentStorageInterface::class);
        $this->parser = $this->createMock(ConfigurationDocumentParserInterface::class);

        $this->storage->method('getDocument')->willReturnCallback(function (string $identifier) {
            foreach ($this->documents as $doc) {
                if ($doc['identifier'] === $identifier) {
                    return $doc['document'];
                }
            }

            return '';
        });

        $this->parser->method('parseDocument')->willReturnCallback(function (string $document) {
            foreach ($this->documents as $doc) {
                if ($doc['document'] === $document) {
                    return $doc['configuration'];
                }
            }

            return [];
        });

        $this->registerDocument('SYS:defaults', 'sysDefaultDocumentContent', static::SYS_DEFAULTS_CONFIGURATION);
        $this->subject = new ConfigurationDocumentManager($this->storage, $this->parser, $this->staticStorage);
    }

    /**
     * @param array<mixed> $configuration
     */
    protected function registerDocument(string $identifier, string $document, array $configuration): void
    {
        $this->documents[] = [
            'identifier' => $identifier,
            'document' => $document,
            'configuration' => $configuration,
        ];
    }

    /**
     * @param array<string> $ids
     *
     * @return array<string,array{uuid:string,weight:int,value:string}>
     */
    protected static function createIncludeList(array $ids): array
    {
        $weight = 10;
        $list = [];
        foreach ($ids as $id) {
            $list['id-' . $id] = [
                'uuid' => 'id-' . $id,
                'weight' => $weight,
                'value' => $id,
            ];
            $weight += 10;
        }

        return $list;
    }

    /**
     * @return array<array{array<array{identifier:string,document:string,configuration:array<mixed>}>,string,array<array<mixed>>}>
     */
    public static function getConfigurationStackFromIdentifierProvider(): array
    {
        return [
            'noIncludes' => [
                [
                    ['identifier' => 'id1', 'document' => 'documentContent1', 'configuration' => ['key1' => 'value1']],
                ],
                'id1',
                [
                    ['key1' => 'value1'],
                ],
            ],
            'simpleInclude' => [
                [
                    [
                        'identifier' => 'id1',
                        'document' => 'documentContent1',
                        'configuration' => [
                            'key1' => 'value1',
                            'metaData' => [
                                'includes' => self::createIncludeList(['id2']),
                            ],
                        ],
                    ],
                    [
                        'identifier' => 'id2',
                        'document' => 'documentContent2',
                        'configuration' => [
                            'key2' => 'value2',
                        ],
                    ],
                ],
                'id1',
                [
                    ['key2' => 'value2'],
                    ['key1' => 'value1'],
                ],
            ],
            'nestedInclude' => [
                [
                    [
                        'identifier' => 'id1',
                        'document' => 'documentContent1',
                        'configuration' => [
                            'key1' => 'value1',
                            'metaData' => [
                                'includes' => self::createIncludeList(['id2']),
                            ],
                        ],
                    ],
                    [
                        'identifier' => 'id2',
                        'document' => 'documentContent2',
                        'configuration' => [
                            'key2' => 'value2',
                            'metaData' => [
                                'includes' => self::createIncludeList(['id3', 'id4']),
                            ],
                        ],
                    ],
                    [
                        'identifier' => 'id3',
                        'document' => 'documentContent3',
                        'configuration' => [
                            'key3' => 'value3',
                        ],
                    ],
                    [
                        'identifier' => 'id4',
                        'document' => 'documentContent4',
                        'configuration' => [
                            'key4' => 'value4',
                        ],
                    ],
                ],
                'id1',
                [
                    ['key3' => 'value3'],
                    ['key4' => 'value4'],
                    ['key2' => 'value2'],
                    ['key1' => 'value1'],
                ],
            ],
            'doubleInclude' => [
                [
                    [
                        'identifier' => 'id1',
                        'document' => 'documentContent1',
                        'configuration' => [
                            'key1' => 'value1',
                            'metaData' => [
                                'includes' => self::createIncludeList(['id2', 'id3']),
                            ],
                        ],
                    ],
                    [
                        'identifier' => 'id2',
                        'document' => 'documentContent2',
                        'configuration' => [
                            'key2' => 'value2',
                            'metaData' => [
                                'includes' => self::createIncludeList(['id4']),
                            ],
                        ],
                    ],
                    [
                        'identifier' => 'id3',
                        'document' => 'documentContent3',
                        'configuration' => [
                            'key3' => 'value3',
                            'metaData' => [
                                'includes' => self::createIncludeList(['id4']),
                            ],
                        ],
                    ],
                    [
                        'identifier' => 'id4',
                        'document' => 'documentContent4',
                        'configuration' => [
                            'key4' => 'value4',
                        ],
                    ],
                ],
                'id1',
                [
                    ['key4' => 'value4'],
                    ['key2' => 'value2'],
                    ['key3' => 'value3'],
                    ['key1' => 'value1'],
                ],
            ],
        ];
    }

    /**
     * @param array<array{identifier:string,document:string,configuration:array<mixed>}> $docs
     * @param array<array<mixed>> $expectedResult
     */
    #[Test]
    #[DataProvider('getConfigurationStackFromIdentifierProvider')]
    public function getConfigurationStackFromIdentifier(array $docs, string $id, array $expectedResult): void
    {
        foreach ($docs as $doc) {
            $this->registerDocument($doc['identifier'], $doc['document'], $doc['configuration']);
        }

        $stack = $this->subject->getConfigurationStackFromIdentifier($id);
        $stack = array_map(static function (array $config) {
            unset($config['metaData']);

            return $config;
        }, $stack);

        // SYS:defaults is always prepended to every stack
        $sysDefaults = array_shift($stack);
        $this->assertEquals(static::SYS_DEFAULTS_CONFIGURATION, $sysDefaults);

        $this->assertEquals($expectedResult, $stack);
    }

    #[Test]
    public function getConfigurationStackFromIdentifierLoop(): void
    {
        $this->registerDocument('id1', 'documentContent1', [
            'key1' => 'value1',
            'metaData' => [
                'includes' => $this->createIncludeList(['id2']),
            ],
        ]);
        $this->registerDocument('id2', 'documentContent2', [
            'key2' => 'value2',
            'metaData' => [
                'includes' => $this->createIncludeList(['id1']),
            ],
        ]);

        $this->expectException(ConfigurationDocumentIncludeLoopException::class);

        $this->subject->getConfigurationStackFromIdentifier('id1');
    }

    #[Test]
    public function getConfigurationStackFromIdentifierNestedLoop(): void
    {
        $this->registerDocument('id1', 'documentContent1', [
            'key1' => 'value1',
            'metaData' => [
                'includes' => $this->createIncludeList(['id2']),
            ],
        ]);
        $this->registerDocument('id2', 'documentContent2', [
            'key2' => 'value2',
            'metaData' => [
                'includes' => $this->createIncludeList(['id2']),
            ],
        ]);
        $this->registerDocument('id3', 'documentContent3', [
            'key3' => 'value3',
            'metaData' => [
                'includes' => $this->createIncludeList(['id1']),
            ],
        ]);

        $this->expectException(ConfigurationDocumentIncludeLoopException::class);

        $this->subject->getConfigurationStackFromIdentifier('id1');
    }

    // TODO implement more tests
}

<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\FileValue;

/**
 * @extends AbstractFieldTest<FileValue>
 */
class FileValueTest extends AbstractFieldTest
{
    protected const FIELD_CLASS = FileValue::class;

    protected function createField(mixed ...$arguments): FileValue
    {
        return new FileValue(
            $arguments[2] ?? 'path1',
            $arguments[0] ?? 'name1',
            $arguments[1] ?? 'url1',
            $arguments[3] ?? 'type1'
        );
    }

    /** @test */
    public function init(): void
    {
        $this->subject = $this->createField();
        $this->assertEquals('name1', $this->subject->getFileName());
        $this->assertEquals('url1', $this->subject->getPublicUrl());
        $this->assertEquals('path1', $this->subject->getRelativePath());
        $this->assertEquals('type1', $this->subject->getMimeType());
    }

    /**
     * @return array<array{0:array{0:string,1:string,2:string,3:string},1:string}>
     */
    public function castToStringProvider(): array
    {
        return [
            [['name1', 'url1', 'path1', 'type1'], 'url1'],
            [['name2', 'url2', 'path2', 'type2'], 'url2'],
        ];
    }

    /**
     * @return array<array{0:array{0:string,1:string,2:string,3:string},1:array{fileName:string,publicUrl:string,relativePath:string,mimeType:string}}>
     */
    public function packProvider(): array
    {
        return [
            [
                ['name1', 'url1', 'path1', 'type1'],
                [
                    'fileName' => 'name1',
                    'publicUrl' => 'url1',
                    'relativePath' => 'path1',
                    'mimeType' => 'type1',
                ],
            ],
        ];
    }
}

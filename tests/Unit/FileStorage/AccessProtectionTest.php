<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\FileStorage;

use DigitalMarketingFramework\Core\FileStorage\AccessProtectionTrait;
use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AccessProtectionTest extends TestCase
{
    protected mixed $originalServerSoftware = null;

    protected function setUp(): void
    {
        $this->originalServerSoftware = $_SERVER['SERVER_SOFTWARE'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->originalServerSoftware === null) {
            unset($_SERVER['SERVER_SOFTWARE']);
        } else {
            $_SERVER['SERVER_SOFTWARE'] = $this->originalServerSoftware;
        }
    }

    protected function subject(bool $public, bool $accessFileExists): object
    {
        return new class($public, $accessFileExists) {
            use AccessProtectionTrait;

            public const ACCESS_FILE_NAME = FileStorageInterface::ACCESS_FILE_NAME;

            public function __construct(
                protected bool $public,
                protected bool $accessFileExists,
            ) {
            }

            public function isPubliclyAccessible(string $identifier): bool
            {
                return $this->public;
            }

            public function fileExists(string $fileIdentifier): bool
            {
                return $this->accessFileExists;
            }
        };
    }

    /**
     * @return array<string,array{string,bool,bool,bool}>
     */
    public static function protectionProvider(): array
    {
        return [
            // A storage nothing serves is safe whatever else is true, which is the outcome to
            // aim for and the only one that holds on every server.
            'non-public storage, apache' => ['Apache/2.4', false, false, true],
            'non-public storage, nginx' => ['nginx/1.24', false, false, true],

            'public storage with access file, apache' => ['Apache/2.4', true, true, true],
            'public storage without access file, apache' => ['Apache/2.4', true, false, false],

            // nginx ignores the file, so its presence proves nothing and must not silence the
            // warning.
            'public storage with access file, nginx' => ['nginx/1.24', true, true, false],
            'public storage without access file, nginx' => ['nginx/1.24', true, false, false],
        ];
    }

    #[Test]
    #[DataProvider('protectionProvider')]
    public function protectionDependsOnTheServerAsWellAsTheFile(string $server, bool $public, bool $accessFileExists, bool $expected): void
    {
        $_SERVER['SERVER_SOFTWARE'] = $server;

        $this->assertSame($expected, $this->subject($public, $accessFileExists)->folderIsProtected('1:/some/folder'));
    }
}

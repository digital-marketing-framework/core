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

    protected function subject(bool $public, bool $folderExists, bool $accessFileExists): object
    {
        return new class($public, $folderExists, $accessFileExists) {
            use AccessProtectionTrait;

            public const ACCESS_FILE_NAME = FileStorageInterface::ACCESS_FILE_NAME;

            public function __construct(
                protected bool $public,
                protected bool $folderExists,
                protected bool $accessFileExists,
            ) {
            }

            public function isPubliclyAccessible(string $identifier): bool
            {
                return $this->public;
            }

            public function folderExists(string $folderIdentifier): bool
            {
                return $this->folderExists;
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

        $this->assertSame($expected, $this->subject($public, true, $accessFileExists)->folderIsProtected('1:/some/folder'));
    }

    /**
     * @return array<string,array{string,bool}>
     */
    public static function missingFolderProvider(): array
    {
        return [
            // Nothing is stored yet, and the first file written creates the folder with its
            // access file, so there is nothing to warn about ahead of time.
            'apache' => ['Apache/2.4', true],

            // Here the warning has to come before the folder does: this server will serve
            // whatever lands in it and no file can stop that, so the only thing that can change
            // the answer is moving the storage — which is what the warning says to do.
            'nginx' => ['nginx/1.24', false],
        ];
    }

    #[Test]
    #[DataProvider('missingFolderProvider')]
    public function aMissingFolderIsJudgedByWhatWritingToItWouldMean(string $server, bool $expected): void
    {
        $_SERVER['SERVER_SOFTWARE'] = $server;

        $this->assertSame($expected, $this->subject(true, false, false)->folderIsProtected('1:/some/folder'));
    }
}

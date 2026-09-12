<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\FileStorage;

use DigitalMarketingFramework\Core\FileStorage\FileStorage;
use DigitalMarketingFramework\Core\Log\LoggerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the plain file storage itself.
 *
 * Everything else in the suite stubs FileStorageInterface, which means a stub can agree with
 * what callers expect while the implementation does not — and that is exactly how getFileName()
 * and getFileBaseName() came to be swapped here relative to the TYPO3 and Drupal storages.
 */
class FileStorageTest extends TestCase
{
    protected string $folder;

    protected FileStorage $subject;

    protected mixed $originalServerSoftware = null;

    protected function setUp(): void
    {
        $this->originalServerSoftware = $_SERVER['SERVER_SOFTWARE'] ?? null;
        $this->folder = sys_get_temp_dir() . '/dmf-file-storage-' . uniqid();
        mkdir($this->folder);

        $this->subject = new FileStorage();
        $this->subject->setLogger($this->createMock(LoggerInterface::class));
    }

    protected function tearDown(): void
    {
        if ($this->originalServerSoftware === null) {
            unset($_SERVER['SERVER_SOFTWARE']);
        } else {
            $_SERVER['SERVER_SOFTWARE'] = $this->originalServerSoftware;
        }

        $files = glob($this->folder . '/*');
        foreach ($files === false ? [] : $files as $file) {
            unlink($file);
        }

        rmdir($this->folder);
    }

    #[Test]
    public function nameCarriesTheExtensionAndBaseNameDoesNot(): void
    {
        $fileIdentifier = $this->folder . '/main.config.yaml';
        file_put_contents($fileIdentifier, '');

        $this->assertSame('main.config.yaml', $this->subject->getFileName($fileIdentifier));
        $this->assertSame('main.config', $this->subject->getFileBaseName($fileIdentifier));
        $this->assertSame('yaml', $this->subject->getFileExtension($fileIdentifier));
    }

    #[Test]
    public function aFileThatIsNotThereHasNoName(): void
    {
        $fileIdentifier = $this->folder . '/absent.yaml';

        $this->assertNull($this->subject->getFileName($fileIdentifier));
        $this->assertNull($this->subject->getFileBaseName($fileIdentifier));
    }

    #[Test]
    public function writingIntoAFolderThatWasAlreadyThereStillProtectsIt(): void
    {
        // The folder is not created by this write, so nothing else in the chain would reach
        // protectFolder() — and a folder configured before the access file existed is exactly
        // the case that needs it.
        $_SERVER['SERVER_SOFTWARE'] = 'Apache/2.4';
        $this->assertFileDoesNotExist($this->folder . '/.htaccess');

        $this->subject->putFileContents($this->folder . '/some.config.yaml', 'content');

        $this->assertFileExists($this->folder . '/.htaccess');
    }

    #[Test]
    public function copyingAFileKeepsItsName(): void
    {
        $fileIdentifier = $this->folder . '/source.fields.yaml';
        file_put_contents($fileIdentifier, 'content');
        $target = $this->folder . '/sub';
        mkdir($target);

        $this->assertSame($target . '/source.fields.yaml', $this->subject->copyFileToFolder($fileIdentifier, $target));

        unlink($target . '/source.fields.yaml');
        rmdir($target);
    }
}

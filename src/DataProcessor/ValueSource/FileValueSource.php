<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareInterface;
use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareTrait;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class FileValueSource extends ValueSource implements FileStorageAwareInterface
{
    use FileStorageAwareTrait;

    public const KEY_NAME = 'name';

    public const KEY_PATH = 'path';

    public const KEY_URL = 'url';

    public const KEY_MIMETYPE = 'mimetype';

    public function build(): string|ValueInterface|null
    {
        $fileName = $this->dataProcessor->processValue($this->getConfig(static::KEY_NAME), $this->context->copy());
        $fileIdentifier = $this->dataProcessor->processValue($this->getConfig(static::KEY_PATH), $this->context->copy());
        $fileUrl = $this->dataProcessor->processValue($this->getConfig(static::KEY_URL), $this->context->copy());
        $mimeType = $this->dataProcessor->processValue($this->getConfig(static::KEY_MIMETYPE), $this->context->copy());

        if ($this->fileStorage->fileExists((string)$fileIdentifier)) {
            if (GeneralUtility::isEmpty($fileName)) {
                $fileName = $this->fileStorage->getFileName($fileIdentifier);
            }

            if (GeneralUtility::isEmpty($fileUrl)) {
                $fileUrl = $this->fileStorage->getPublicUrl($fileIdentifier);
            }

            if (GeneralUtility::isEmpty($mimeType)) {
                $mimeType = $this->fileStorage->getMimeType($fileIdentifier);
            }
        }

        if (
            !GeneralUtility::isEmpty($fileIdentifier)
            && !GeneralUtility::isEmpty($fileName)
            && !GeneralUtility::isEmpty($fileUrl)
            && !GeneralUtility::isEmpty($mimeType)
        ) {
            $fileField = [
                'fileName' => (string)$fileName,
                'publicUrl' => (string)$fileUrl,
                'relativePath' => (string)$fileIdentifier,
                'mimeType' => (string)$mimeType,
            ];

            return FileValue::unpack($fileField);
        }

        return null;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();

        $pathSchema = new CustomSchema(ValueSchema::TYPE);
        $pathSchema->getRenderingDefinition()->setLabel('File Identifier');
        $schema->addProperty(static::KEY_PATH, $pathSchema);

        $nameSchema = new CustomSchema(ValueSchema::TYPE);
        $nameSchema->getRenderingDefinition()->setLabel('File Name (optional)');
        $schema->addProperty(static::KEY_NAME, $nameSchema);

        $urlSchema = new CustomSchema(ValueSchema::TYPE);
        $urlSchema->getRenderingDefinition()->setLabel('Public URL (optional)');
        $schema->addProperty(static::KEY_URL, $urlSchema);

        $mimeTypeSchema = new CustomSchema(ValueSchema::TYPE);
        $mimeTypeSchema->getRenderingDefinition()->setLabel('MIME Type (optional)');
        $schema->addProperty(static::KEY_MIMETYPE, $mimeTypeSchema);

        return $schema;
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}

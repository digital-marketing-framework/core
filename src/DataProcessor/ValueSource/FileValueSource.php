<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class FileValueSource extends ValueSource
{
    public const KEY_NAME = 'name';

    public const DEFAULT_NAME = [];

    public const KEY_PATH = 'path';

    public const DEFAULT_PATH = [];

    public const KEY_URL = 'url';

    public const DEFAULT_URL = [];

    public const KEY_MIMETYPE = 'mimetype';

    public const DEFAULT_MIMETYPE = [];

    public function build(): null|string|ValueInterface
    {
        $fileName = $this->dataProcessor->processValue($this->getConfig(static::KEY_NAME), $this->context->copy());
        $filePath = $this->dataProcessor->processValue($this->getConfig(static::KEY_PATH), $this->context->copy());
        $fileUrl = $this->dataProcessor->processValue($this->getConfig(static::KEY_URL), $this->context->copy());
        $mimeType = $this->dataProcessor->processValue($this->getConfig(static::KEY_MIMETYPE), $this->context->copy());

        if (file_exists((string)$filePath)) {
            if (GeneralUtility::isEmpty($fileName)) {
                $fileName = pathinfo($filePath)['filename'];
            }

            if (GeneralUtility::isEmpty($fileUrl)) {
                $fileUrl = $filePath;
            }

            if (GeneralUtility::isEmpty($mimeType)) {
                $mimeType = mime_content_type($filePath);
            }
        }

        if (
            !GeneralUtility::isEmpty($fileName)
            && !GeneralUtility::isEmpty($filePath)
            && !GeneralUtility::isEmpty($fileUrl)
            && !GeneralUtility::isEmpty($mimeType)
        ) {
            $fileField = [
                'fileName' => (string)$fileName,
                'publicUrl' => (string)$fileUrl,
                'relativePath' => (string)$filePath,
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
        $schema->addProperty(static::KEY_NAME, new CustomSchema(ValueSchema::TYPE));
        $schema->addProperty(static::KEY_PATH, new CustomSchema(ValueSchema::TYPE));
        $schema->addProperty(static::KEY_URL, new CustomSchema(ValueSchema::TYPE));
        $schema->addProperty(static::KEY_MIMETYPE, new CustomSchema(ValueSchema::TYPE));

        return $schema;
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}

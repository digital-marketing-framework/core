<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class FileContentResolver extends ContentResolver
{
    protected const KEY_NAME = 'name';
    protected const DEFAULT_NAME = '';

    protected const KEY_PATH = 'path';
    protected const DEFAULT_PATH = '';
    
    protected const KEY_URL = 'url';
    protected const DEFAULT_URL = '';

    protected const KEY_MIMETYPE = 'mimetype';
    protected const DEFAULT_MIMETYPE = '';

    public function build(): string|ValueInterface|null
    {
        if (is_array($this->configuration)) {
            $fileName = $this->resolveContent($this->getConfig(static::KEY_NAME));
            $filePath = $this->resolveContent($this->getConfig(static::KEY_PATH));
            $fileUrl = $this->resolveContent($this->getConfig(static::KEY_URL));
            $mimeType = $this->resolveContent($this->getConfig(static::KEY_MIMETYPE));
        } else {
            $fileName = '';
            $filePath = $this->configuration;
            $fileUrl = '';
            $mimeType = '';
        }
        
        if (file_exists($filePath)) {
            if ($fileName === '') {
                $fileName = pathinfo($filePath)['filename'];
            }
            if ($fileUrl === '') {
                $fileUrl = $filePath;
            }
            if ($mimeType === '') {
                $mimeType = mime_content_type($filePath);
            }
        }
        
        if ($fileName !== '' && $filePath !== '' && $fileUrl !== '' && $mimeType !== '') {
            $fileField = array(
                'fileName' => $fileName,
                'publicUrl' => $fileUrl,
                'relativePath' => $filePath,
                'mimeType' => $mimeType
            );
            return FileValue::unpack($fileField);
        }

        return null;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_NAME => static::DEFAULT_NAME,
            static::KEY_PATH => static::DEFAULT_PATH,
            static::KEY_URL => static::DEFAULT_URL,
            static::KEY_MIMETYPE => static::DEFAULT_MIMETYPE,
        ];
    }
}

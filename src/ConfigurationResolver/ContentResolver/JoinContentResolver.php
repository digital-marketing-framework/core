<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class JoinContentResolver extends ContentResolver
{
    protected const KEY_GLUE = 'glue';
    protected const DEFAULT_GLUE = '\\n';

    public function finish(&$result): bool
    {
        if ($result instanceof MultiValue) {
            $glue = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_GLUE));
            $result->setGlue($glue);
            $result = (string)$result;
        }
        return false;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEYWORD_GLUE => static::DEFAULT_GLUE,
        ];
    }
}

<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class JoinContentResolver extends ContentResolver
{
    protected const KEY_GLUE = 'glue';
    protected const DEFAULT_GLUE = '\\n';

    public function finish(string|ValueInterface|null &$result): bool
    {
        if ($result instanceof MultiValueInterface) {
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

<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationBehaviour;
use DigitalMarketingFramework\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class GeneralContentResolver extends ContentResolver implements GeneralConfigurationResolverInterface
{
    protected string $glue = '';

    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::ConvertScalarToArrayWithSelfValue;
    }

    protected function add(string|ValueInterface|null $result, string|ValueInterface|null $content): string|ValueInterface|null
    {
        if ($content !== null) {
            if ($result === null || $result === '') {
                $result = $content;
            } elseif ((string)$content !== '') {
                $result .= $this->glue ?: '';
                $result .= (string)$content;
            }
        }
        return $result;
    }

    public function build(): string|ValueInterface|null
    {
        if (array_key_exists(static::KEYWORD_GLUE, $this->configuration)) {
            $glue = $this->resolveContent($this->configuration[static::KEYWORD_GLUE]);
            if ($glue !== null) {
                $this->glue = GeneralUtility::parseSeparatorString($glue);
            }
            unset($this->configuration[static::KEYWORD_GLUE]);
        }

        $contentResolvers = [];
        foreach ($this->configuration as $key => $value) {
            $contentResolver = $this->resolveKeyword(is_numeric($key) ? 'general' : $key, $value);
            if ($contentResolver) {
                $contentResolvers[] = $contentResolver;
            }
        }

        $this->sortSubResolvers($contentResolvers);

        $result = null;
        foreach ($contentResolvers as $contentResolver) {
            $content = $contentResolver->build();
            $result = $this->add($result, $content);
        }
        foreach ($contentResolvers as $contentResolver) {
            if ($contentResolver->finish($result)) {
                break;
            }
        }
        return $result;
    }

    public function resolve(): string|ValueInterface|null
    {
        $result = $this->build();
        $this->finish($result);
        return $result;
    }
}

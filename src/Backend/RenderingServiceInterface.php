<?php

namespace DigitalMarketingFramework\Core\Backend;

use DigitalMarketingFramework\Core\ConfigurationEditor\MetaData;

interface RenderingServiceInterface
{
    /**
     * @param array<string,string> $parameters
     *
     * @return array<string,string>
     */
    public function getTextAreaDataAttributes(
        bool $ready,
        string $mode,
        bool $readonly,
        bool $globalDocument,
        string $documentType = MetaData::DEFAULT_DOCUMENT_TYPE,
        bool $includes = true,
        array $parameters = [],
        string $contextIdentifier = '',
    ): array;
}

<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

class EvaluationReferenceSchema extends ReferenceSchema
{
    public const TYPE = 'EVALUATION_REFERENCE';

    public function __construct(
        ?string $defaultValue = null,
        bool $required = true,
        ?string $firstEmptyOptionLabel = null
    ) {
        parent::__construct($defaultValue, $required, $firstEmptyOptionLabel);
        $this->getRenderingDefinition()->setLabel('Condition');
    }

    protected function getReferencePath(): string
    {
        return sprintf('/%s/*', ConfigurationInterface::KEY_EVALUATIONS);
    }

    protected function getLabel(): string
    {
        return '{key}';
    }
}

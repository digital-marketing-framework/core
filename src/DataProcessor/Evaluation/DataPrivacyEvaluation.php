<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareTrait;

class DataPrivacyEvaluation extends Evaluation implements DataPrivacyManagerAwareInterface
{
    use DataPrivacyManagerAwareTrait;

    public const KEY_LEVEL = 'level';

    public const DEFAULT_LEVEL = '';

    public function evaluate(): bool
    {
        $level = $this->getConfig(static::KEY_LEVEL);

        return $this->dataPrivacyManager->hasPermission($level);
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_LEVEL, new StringSchema(static::DEFAULT_LEVEL));

        return $schema;
    }
}

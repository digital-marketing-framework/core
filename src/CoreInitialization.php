<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\EqualsComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ExistsComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\InComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsEmptyComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsFalseComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsTrueComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\RegExpComparison;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\ExcludeFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\FieldMapDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\IgnoreEmptyFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PrefixDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\AndEvaluation;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\ComparisonEvaluation;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\FalseEvaluation;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\NotEvaluation;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\OrEvaluation;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\TrueEvaluation;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DefaultValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\IndexValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\InsertDataValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\JoinValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\LowerCaseValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapReferenceValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\NegateValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SpliceValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SprintfValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\TrimValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\UpperCaseValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\BooleanValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConcatenationValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConditionValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldCollectorValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FileValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FirstOfValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\IntegerValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ListValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\MultiValueValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\NullValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;

class CoreInitialization extends Initialization
{
    protected const PLUGINS = [
        RegistryDomain::CORE => [
            ValueSourceInterface::class => [
                BooleanValueSource::class,
                ConcatenationValueSource::class,
                ConditionValueSource::class,
                ConstantValueSource::class,
                FieldCollectorValueSource::class,
                FieldValueSource::class,
                FileValueSource::class,
                FirstOfValueSource::class,
                IntegerValueSource::class,
                ListValueSource::class,
                MultiValueValueSource::class,
                NullValueSource::class,
            ],
            ValueModifierInterface::class => [
                DefaultValueModifier::class,
                IndexValueModifier::class,
                InsertDataValueModifier::class,
                JoinValueModifier::class,
                LowerCaseValueModifier::class,
                MapReferenceValueModifier::class,
                MapValueModifier::class,
                NegateValueModifier::class,
                SpliceValueModifier::class,
                SprintfValueModifier::class,
                TrimValueModifier::class,
                UpperCaseValueModifier::class,
            ],
            EvaluationInterface::class => [
                AndEvaluation::class,
                ComparisonEvaluation::class,
                FalseEvaluation::class,
                NotEvaluation::class,
                OrEvaluation::class,
                TrueEvaluation::class,
            ],
            ComparisonInterface::class => [
                EqualsComparison::class,
                ExistsComparison::class,
                InComparison::class,
                IsEmptyComparison::class,
                IsFalseComparison::class,
                IsTrueComparison::class,
                RegExpComparison::class,
            ],
            DataMapperInterface::class => [
                PrefixDataMapper::class,
                ExcludeFieldsDataMapper::class,
                FieldMapDataMapper::class,
                IgnoreEmptyFieldsDataMapper::class,
                PassthroughFieldsDataMapper::class,
            ],
        ],
    ];

    protected const SCHEMA_MIGRATIONS = [];

    public function __construct(string $packageAlias = '')
    {
        parent::__construct('core', '1.0.0', $packageAlias);
    }
}

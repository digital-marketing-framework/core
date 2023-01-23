<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\BooleanContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\DataMapContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\DataMapPipelineContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\DefaultContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\FieldCollectorContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\FileContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\FirstOfContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IfContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IgnoreContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IgnoreIfContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IgnoreIfEmptyContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IndexContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\InsertDataContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IntegerContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\JoinContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ListContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\LoopDataContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\LowerCaseContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\MapContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\NegateContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\PassthroughFieldsContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\SelfContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\SplitContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\SprintfContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\UpperCaseContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ValueContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\AllEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\AnyEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\EmptyEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\EqualsEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\ExistsEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\FieldEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\IndexEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\InEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\IsFalseEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\IsTrueEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\KeyEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\LowerCaseEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\NotEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\OrEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\ProcessedEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\RegexpEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\RequiredEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\SelfEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\TrimEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\UpperCaseEvaluation;
use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\ValueEvaluation;

class CorePluginInitialization extends PluginInitialization
{
    protected const PLUGINS = [
        EvaluationInterface::class => [
            SelfEvaluation::class,
            AllEvaluation::class,
            AndEvaluation::class,
            AnyEvaluation::class,
            EmptyEvaluation::class,
            EqualsEvaluation::class,
            ExistsEvaluation::class,
            FieldEvaluation::class,
            GeneralEvaluation::class,
            IndexEvaluation::class,
            InEvaluation::class,
            IsFalseEvaluation::class,
            IsTrueEvaluation::class,
            KeyEvaluation::class,
            LowerCaseEvaluation::class,
            NotEvaluation::class,
            OrEvaluation::class,
            ProcessedEvaluation::class,
            RegexpEvaluation::class,
            RequiredEvaluation::class,
            TrimEvaluation::class,
            UpperCaseEvaluation::class,
            ValueEvaluation::class,
        ],
        ContentResolverInterface::class => [
            SelfContentResolver::class,
            BooleanContentResolver::class,
            DataMapContentResolver::class,
            DataMapPipelineContentResolver::class,
            DefaultContentResolver::class,
            FieldCollectorContentResolver::class,
            FieldContentResolver::class,
            FileContentResolver::class,
            FirstOfContentResolver::class,
            GeneralContentResolver::class,
            IfContentResolver::class,
            IgnoreContentResolver::class,
            IgnoreIfContentResolver::class,
            IgnoreIfEmptyContentResolver::class,
            IndexContentResolver::class,
            InsertDataContentResolver::class,
            IntegerContentResolver::class,
            JoinContentResolver::class,
            ListContentResolver::class,
            LoopDataContentResolver::class,
            LowerCaseContentResolver::class,
            MapContentResolver::class,
            MultiValueContentResolver::class,
            NegateContentResolver::class,
            PassthroughFieldsContentResolver::class,
            SplitContentResolver::class,
            SprintfContentResolver::class,
            TrimContentResolver::class,
            UpperCaseContentResolver::class,
            ValueContentResolver::class,
        ],
    ];
}

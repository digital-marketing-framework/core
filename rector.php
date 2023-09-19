<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->importNames(true, true);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        SetList::CODING_STYLE,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
    ]);

    $rectorConfig->skip([
        ClosureToArrowFunctionRector::class,
        FinalizePublicClassConstantRector::class,
        RemoveNonExistingVarAnnotationRector::class, // conflicts with phpstan
        RemoveUselessReturnTagRector::class, // conflicts with phpstan
        CatchExceptionNameMatchingTypeRector::class,
        AddLiteralSeparatorToNumberRector::class,
    ]);
};

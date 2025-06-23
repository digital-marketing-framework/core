<?php

namespace DigitalMarketingFramework\Core\Utility;

use DateTime;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Queue\JobInterface;
use DigitalMarketingFramework\Core\Queue\QueueInterface;

class QueueUtility
{
    public const STATUS_MESSAGE_PATTERN = '/(\\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}:\\d{2}): (.+)/';

    /**
     * @return array<array{timestamp:DateTime,message:string,job:JobInterface}>
     */
    public static function getErrors(JobInterface $job, bool $latestErrorOnly = false): array
    {
        if ($job->getStatus() !== QueueInterface::STATUS_FAILED) {
            return [];
        }

        $errors = [];

        $messages = $job->getStatusMessage();
        preg_match_all(static::STATUS_MESSAGE_PATTERN, $messages, $matches);

        $messageDates = $matches[1] ?? [];
        $messageStrings = $matches[2] ?? [];
        foreach ($messageDates as $index => $date) {
            $message = $messageStrings[$index] ?? '';
            $errors[] = [
                'timestamp' => new DateTime($date),
                'message' => trim($message),
                'job' => $job,
            ];
        }

        if ($errors === []) {
            $errors[] = [
                'timestamp' => $job->getChanged(),
                'message' => trim($job->getStatusMessage()),
                'job' => $job,
            ];
        }

        static::sortErrors($errors);

        if ($errors === []) {
            return [];
        }

        if ($latestErrorOnly) {
            return [array_pop($errors)];
        }

        return $errors;
    }

    /**
     * @param array<array{timestamp:DateTime,message:string,job:JobInterface}> $errors
     */
    public static function sortErrors(array &$errors): void
    {
        usort($errors, fn (array $a, array $b) => $a['timestamp']->getTimestamp() <=> $b['timestamp']->getTimestamp());
    }

    /**
     * @param array<JobInterface> $jobs
     *
     * @return array<array{message:string,count:int,firstSeen:JobInterface,firstSeenTime:DateTime,lastSeen:JobInterface,lastSeenTime:DateTime,types:array<string,int>}>
     */
    public static function getErrorStatistics(array $jobs, bool $latestErrorOnly = false): array
    {
        $errors = [];
        foreach ($jobs as $job) {
            array_push($errors, ...static::getErrors($job, $latestErrorOnly));
        }

        static::sortErrors($errors);

        $statistics = [];
        foreach ($errors as $error) {
            $message = $error['message'];
            $type = $error['job']->getType();
            $statistics[$message] ??= [
                'message' => $message,
                'count' => 0,
                'firstSeen' => $error['job'],
                'firstSeenTime' => $error['timestamp'],
                'types' => [],
            ];
            ++$statistics[$message]['count'];
            $statistics[$message]['types'][$type] ??= 0;
            ++$statistics[$message]['types'][$type];
            $statistics[$message]['lastSeen'] = $error['job'];
            $statistics[$message]['lastSeenTime'] = $error['timestamp'];
        }

        return array_values($statistics);
    }

    /**
     * @param array<array{message:string,count:int,firstSeen:JobInterface,firstSeenTime:DateTime,lastSeen:JobInterface,lastSeenTime:DateTime,types:array<string,int>}> $statistics
     * @param ?array{sorting?:array<string,""|"ASC"|"DESC">} $navigation
     */
    public static function applyNavigationToErrorStatistics(array &$statistics, ?array $navigation): void
    {
        $navigation['sorting'] ??= [];
        if ($navigation['sorting'] === []) {
            return;
        }

        usort($statistics, static function (array $row1, array $row2) use ($navigation) {
            $sortDirection = 'DESC';
            $value1 = 0;
            $value2 = 0;
            foreach ($navigation['sorting'] as $sort => $direction) {
                if ($direction === '') {
                    continue;
                }

                $sortDirection = $direction;
                $value1 = match ($sort) {
                    'count' => $row1['count'],
                    'lastSeen' => $row1['lastSeenTime']->getTimestamp(),
                    'firstSeen' => $row1['firstSeenTime']->getTimestamp(),
                    default => throw new DigitalMarketingFrameworkException(sprintf('unknown sort attribute "%s"', $sort), 6991592528),
                };
                $value2 = match ($sort) {
                    'count' => $row2['count'],
                    'lastSeen' => $row2['lastSeenTime']->getTimestamp(),
                    'firstSeen' => $row2['firstSeenTime']->getTimestamp(),
                    default => throw new DigitalMarketingFrameworkException(sprintf('unknown sort attribute "%s"', $sort), 8729504902),
                };
                if ($value1 !== $value2) {
                    break;
                }
            }

            return $sortDirection === 'ASC' ? $value1 <=> $value2 : $value2 <=> $value1;
        });
    }
}

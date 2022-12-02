<?php

namespace App\Services;

use Carbon\Traits\Date;
use DateTime;

class ProgressService
{
    const ON_TRACK = "on track";
    const NOT_ON_TRACK = "not on track";
    const OVERDUE = "overdue";

    /**
     * @param array $requestData
     * @param DateTime $now
     * @return array
     * @throws \Exception
     */
    public function getProgressSummary(array $requestData, DateTime $now): array
    {
        $creationDate = DateTime::createFromFormat(DateTime::RFC3339, $requestData['creation_date']);
        $dueDate = DateTime::createFromFormat(DateTime::RFC3339, $requestData['due_date']);

        //first overdue check goes there - no point in going further if due date is in the past
        if ($dueDate->getTimestamp() - $now->getTimestamp() < 0 && $requestData['current_progress'] != 100) {
            return ["progress_status" => self::OVERDUE, "expected_progress" => 100];
        }

        $daysPassed = $now->diff($creationDate)->days; //excluding today
        $daysLeft = max($now->diff($dueDate)->days, 1);//excluding today, unless whole term is 1 day
        $totalDays = $creationDate->diff($dueDate)->days;

        $totalLearningLeft = $requestData['course_duration'] * (100 - $requestData['current_progress']) / 100;
        $enoughTimeToday = $this->isEnoughTimeToday($now, $totalLearningLeft, $daysLeft);

        //final results for the output
        $dailyLearningNeeded = $totalLearningLeft / ($daysLeft + $enoughTimeToday);
        $expectedProgress = ($daysPassed + 1) * 100 / max($totalDays, 1);
        $progressStatus = $this->calculateStatus($dailyLearningNeeded, $expectedProgress, $requestData['current_progress']);

        return [
            "progress_status" => $progressStatus,
            "expected_progress" => $progressStatus == self::OVERDUE ? 100 : round($expectedProgress),
            "needed_daily_learning_time" => $progressStatus == self::OVERDUE ? null : round($dailyLearningNeeded)
        ];
    }

    /**
     * Corner case - what if user is on track and we recommend some daily learning time,
     * but today is not enough time left?
     * @param DateTime $now
     * @param int $learningLeft
     * @param int $daysLeft
     * @return int
     * @throws \Exception
     */
    public function isEnoughTimeToday(DateTime $now, int $learningLeft, int $daysLeft): int
    {
        $todayEnd = new DateTime('now', new \DateTimeZone('Europe/Sofia'));
        $todayEnd->setTime(23, 59, 59);
        $leftToday = $todayEnd->getTimestamp() - $now->getTimestamp();
        return ($leftToday - $learningLeft / ($daysLeft + 1)) > 0 ? 1 : 0;
    }

    /**
     * @param int $dailyLearningNeeded
     * @param int $expectedProgress
     * @param int $currentProgress
     * @return string
     */
    public function calculateStatus(int $dailyLearningNeeded, int $expectedProgress, int $currentProgress): string
    {
        //in case calculated learning time is overwhelmingly great
        if ($dailyLearningNeeded > 24 * 60 * 60) {
            return self::OVERDUE;
        }

        if ($expectedProgress > $currentProgress) {
            return self::NOT_ON_TRACK;
        }

        return self::ON_TRACK;
    }
}

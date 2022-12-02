<?php

namespace Tests\Unit;

use App\Services\ProgressService;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @property ProgressService $service
 */
class ProgressServiceTest extends TestCase
{
    private ProgressService $service;

    public function __construct()
    {
        $this->service = new ProgressService();
        parent::__construct();
    }

    public function test_getProgressSummary(): void
    {
        $now = new DateTime('now', new \DateTimeZone("Europe/Sofia"));
        $now->setDate(2022, 12, 05);

        $now->setTime(23, 59, 59);
        $requestData = [
            'creation_date' => '2022-12-01T00:00:00+02:00',
            'due_date' => '2022-12-11T00:00:00+02:00',
            'current_progress' => 50,
            'course_duration' => 1000
        ];
        $result = $this->service->getProgressSummary($requestData, $now);
        $this->assertEquals([
            "progress_status" => "on track",
            "expected_progress" => 50,
            "needed_daily_learning_time" => 100
        ], $result);
    }


    public function test_isEnoughTimeToday(): void
    {
        $now = new DateTime('now', new \DateTimeZone("Europe/Sofia"));

        $now->setTime(01, 59, 59);
        $this->assertEquals(1, $this->service->isEnoughTimeToday($now, 1000, 10));

        $now->setTime(23, 59, 59);
        $this->assertEquals(0, $this->service->isEnoughTimeToday($now, 1000, 10));
    }

    public function test_calculateStatus(): void
    {
        $this->assertEquals($this->service::OVERDUE, $this->service->calculateStatus(24*60*61, 10, 90));
        $this->assertEquals($this->service::ON_TRACK, $this->service->calculateStatus(100, 10, 90));
        $this->assertEquals($this->service::NOT_ON_TRACK, $this->service->calculateStatus(100, 90, 10));
    }
}

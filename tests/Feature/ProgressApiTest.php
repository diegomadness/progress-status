<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProgressApiTest extends TestCase
{

    /**
     * @return void
     */
    public function test_successful_response(): void
    {
        $response = $this->get('/api/progress?course_duration=1000&current_progress=11&creation_date=2022-12-02T00:00:00%2B02:00&due_date=2022-12-11T00:00:00%2B02:00');
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_overdue(): void
    {
        $response = $this->get('/api/progress?course_duration=10000000000000&current_progress=11&creation_date=2022-12-02T00:00:00%2B02:00&due_date=2022-12-11T00:00:00%2B02:00');
        $response->assertContent("{\"progress_status\":\"overdue\",\"expected_progress\":100,\"needed_daily_learning_time\":null}");

        $response = $this->get('/api/progress?course_duration=1000&current_progress=11&creation_date=1000-12-02T00:00:00%2B02:00&due_date=1000-12-11T00:00:00%2B02:00');
        $response->assertContent("{\"progress_status\":\"overdue\",\"expected_progress\":100}");
    }

    /**
     * @return void
     */
    public function test_missing_params(): void
    {
        $response = $this->get('/api/progress?current_progress=11&creation_date=2022-12-02T00:00:00%2B02:00&due_date=2022-12-11T00:00:00%2B02:00');
        $response->assertStatus(400);

        $response = $this->get('/api/progress?course_duration=1000&creation_date=2022-12-02T00:00:00%2B02:00&due_date=2022-12-11T00:00:00%2B02:00');
        $response->assertStatus(400);

        $response = $this->get('/api/progress?course_duration=1000&current_progress=11&due_date=2022-12-11T00:00:00%2B02:00');
        $response->assertStatus(400);

        $response = $this->get('/api/progress?course_duration=1000&current_progress=11&creation_date=2022-12-02T00:00:00%2B02:00');
        $response->assertStatus(400);
    }

    /**
     * @return void
     */
    public function test_wrong_params(): void
    {
        $response = $this->get('/api/progress?course_duration=qwe&current_progress=11&creation_date=2022-12-02T00:00:00%2B02:00&due_date=2022-12-11T00:00:00%2B02:00');
        $response->assertStatus(400);

        $response = $this->get('/api/progress?course_duration=1000&current_progress=11.1&creation_date=2022-12-02T00:00:00%2B02:00&due_date=2022-12-11T00:00:00%2B02:00');
        $response->assertStatus(400);

        $response = $this->get('/api/progress?course_duration=1000&current_progress=11&creation_date=2022-12-T00:00:00%2B02:00&due_date=2022-12-11T00:00:00%2B02:00');
        $response->assertStatus(400);

        $response = $this->get('/api/progress?course_duration=1000&current_progress=11&creation_date=2022-12-02T00:00:00%2B02:00&due_date=2022-12-11T00:00:00%2B:00');
        $response->assertStatus(400);
    }
}

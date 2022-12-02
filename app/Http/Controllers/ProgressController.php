<?php

namespace App\Http\Controllers;

use App\Services\ProgressService;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class ProgressController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function progress(Request $request): JsonResponse
    {
        //please use "%2B" instead of a "+" sign in your URLs
        //this logic can also be hidden behind a custom request class, but I've decided not to overcomplicate
        $validator = Validator::make($request->all(), [
            'course_duration' => 'required|integer',
            'current_progress' => 'required|integer',
            'creation_date' => 'required|date_format:Y-m-d\TH:i:sP',
            'due_date' => 'required|date_format:Y-m-d\TH:i:sP|after:creation_date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $progressService = new ProgressService();
        $now = new DateTime('now', new \DateTimeZone("Europe/Sofia"));
        $report = $progressService->getProgressSummary($validator->validated(), $now);

        return response()->json($report, 200);
    }
}

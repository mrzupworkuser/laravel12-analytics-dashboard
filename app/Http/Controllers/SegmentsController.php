<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SegmentsDataRequest;
use App\Http\Resources\StatusBreakdownResource;
use App\Services\SegmentsService;
use Illuminate\Http\JsonResponse;

class SegmentsController extends Controller
{
    public function __construct(private readonly SegmentsService $segmentsService)
    {}
    /**
     * Breakdown of orders by status in a range.
     */
    public function statusBreakdown(SegmentsDataRequest $request): JsonResponse
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
        
        if ($request->validated('start')) {
            $start = now()->parse($request->validated('start'))->startOfDay();
        }
        
        if ($request->validated('end')) {
            $end = now()->parse($request->validated('end'))->endOfDay();
        }

        $breakdown = $this->segmentsService->getStatusBreakdown($start, $end);

        return StatusBreakdownResource::make($breakdown)->response();
    }
}



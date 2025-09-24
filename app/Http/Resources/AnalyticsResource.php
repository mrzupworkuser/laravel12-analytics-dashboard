<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for shaping analytics API responses consistently.
 *
 * @property array $metrics
 * @property array $series
 * @author Manohar Zarkar
 */
class AnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'metrics' => $this->resource['metrics'] ?? [],
            'series' => $this->resource['series'] ?? [],
        ];
    }
}



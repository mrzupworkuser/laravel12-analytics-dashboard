<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AnalyticsService;
use Illuminate\Console\Command;

/**
 * Command to export analytics time-series data as CSV file.
 *
 * @author Manohar Zarkar
 */
class ExportAnalyticsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'analytics:export {--days=14 : Number of days to include} {--path= : Output path for CSV}';

    /**
     * @var string
     */
    protected $description = 'Export analytics time-series as CSV';

    /**
     * Execute the console command to export analytics data as CSV.
     */
    public function handle(AnalyticsService $service): int
    {
        $days = (int) $this->option('days');
        $series = $service->getTimeSeriesData($days);

        // Generate filename with timestamp
        $filename = 'analytics_' . now()->format('Ymd_His') . '.csv';
        $path = $this->option('path') ?: storage_path('app/' . $filename);

        // Write CSV data
        $handle = fopen($path, 'w');
        fputcsv($handle, ['Date', 'Users', 'Revenue', 'Orders', 'Growth %']);

        foreach ($series['labels'] as $i => $label) {
            fputcsv($handle, [
                $label,
                $series['datasets']['users'][$i] ?? 0,
                $series['datasets']['revenue'][$i] ?? 0,
                $series['datasets']['orders'][$i] ?? 0,
                $series['datasets']['growth'][$i] ?? 0,
            ]);
        }
        fclose($handle);

        $this->info('Exported to: ' . $path);
        return self::SUCCESS;
    }
}



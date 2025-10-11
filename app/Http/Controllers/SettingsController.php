<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SettingsRequest;
use App\Services\SettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SettingsController extends Controller
{
    public function __construct(private readonly SettingsService $settingsService)
    {}

    public function index(): View
    {
        return view('analytics.settings', [
            'days' => $this->settingsService->getAnalyticsDays(),
        ]);
    }

    public function save(SettingsRequest $request): RedirectResponse
    {
        $this->settingsService->saveAnalyticsDays(
            $request->validated('days')
        );

        return back()->with('status', 'Settings saved.');
    }
}



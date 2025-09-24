<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('analytics.settings', [
            'days' => (int) session('analytics.days', 14),
        ]);
    }

    public function save(): RedirectResponse
    {
        $validated = request()->validate([
            'days' => ['required', 'integer', 'min:7', 'max:180'],
        ]);

        session(['analytics.days' => (int) $validated['days']]);

        return back()->with('status', 'Settings saved.');
    }
}



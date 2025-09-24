@extends('layouts.app')

@section('content')
    <div class="grid">
        <div class="card" style="grid-column: span 6">
            <h3 style="margin-top:0">Analytics Settings</h3>
            @if(session('status'))
                <div class="muted">{{ session('status') }}</div>
            @endif
            <form method="post" action="{{ route('analytics.settings.save') }}">
                @csrf
                <div style="display:flex;gap:12px;align-items:center;margin:12px 0">
                    <label for="days">Default range (days)</label>
                    <input id="days" name="days" type="number" min="7" max="180" value="{{ old('days', $days) }}" style="padding:8px;border-radius:8px;border:1px solid rgba(255,255,255,.1);background:#0b1020;color:#e5e7eb;width:120px">
                    @error('days')<div class="muted">{{ $message }}</div>@enderror
                </div>
                <button type="submit" style="padding:10px 14px;border-radius:8px;background:#22d3ee;color:#0b1020;border:none;font-weight:600">Save</button>
            </form>
        </div>
    </div>
@endsection



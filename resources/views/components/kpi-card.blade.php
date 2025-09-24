@props(['label' => '', 'value' => '', 'subtitle' => ''])
<div class="card">
    <div class="kpi">
        <div class="label">{{ $label }}</div>
        <div class="value">{{ $value }}</div>
        @if($subtitle)
            <div class="delta muted">{{ $subtitle }}</div>
        @endif
    </div>
    {{ $slot }}

</div>



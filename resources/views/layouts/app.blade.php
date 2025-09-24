<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Analytics Dashboard • Manohar Zarkar</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--bg:#0f172a;--card:#111827;--muted:#94a3b8;--text:#e5e7eb;--accent:#22d3ee;--ok:#34d399;--warn:#f59e0b;--bad:#ef4444}
        *{box-sizing:border-box}
        html,body{margin:0;padding:0;height:100%;font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Helvetica Neue',Arial,'Noto Sans','Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';background:var(--bg);color:var(--text)}
        a{color:inherit;text-decoration:none}
        .container{max-width:1200px;margin:0 auto;padding:24px}
        header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
        .brand{font-weight:700;font-size:20px;letter-spacing:.2px}
        .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:16px}
        .card{background:var(--card);border-radius:12px;padding:16px;border:1px solid rgba(255,255,255,.06)}
        .kpi{display:flex;flex-direction:column;gap:6px}
        .kpi .label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}
        .kpi .value{font-size:22px;font-weight:700}
        .kpi .delta{font-size:12px}
        .row{display:flex;gap:16px;flex-wrap:wrap}
        .chart-card{padding:16px}
        .muted{color:var(--muted)}
        @media (max-width:1024px){.grid{grid-template-columns:repeat(6,1fr)}}
        @media (max-width:640px){.grid{grid-template-columns:repeat(1,1fr)}.container{padding:16px}}
    </style>
    @yield('head')
</head>
<body>
<div class="container">
    <header>
        <div class="brand">Analytics • Manohar Zarkar</div>
        <nav class="muted">Laravel Analytics Widget</nav>
    </header>
    @yield('content')
</div>
@yield('scripts')
</body>
</html>



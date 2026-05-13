<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход / Vexel Admin</title>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#111827;color:#111827;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}.panel{width:min(420px,calc(100vw - 32px));background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:28px;box-shadow:0 24px 80px rgba(0,0,0,.28)}h1,p{margin:0}.muted{margin-top:8px;color:#6b7280}.form{display:grid;gap:16px;margin-top:24px}label{display:grid;gap:8px;color:#4b5563;font-weight:800}input{min-height:44px;border:1px solid #d1d5db;border-radius:8px;padding:0 12px;font:inherit}.button{min-height:44px;border:0;border-radius:8px;background:#2563eb;color:#fff;font-weight:900;cursor:pointer}.errors{padding:12px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;border-radius:8px;font-weight:700}
    </style>
</head>
<body>
    <main class="panel">
        <h1>Vexel Admin</h1>
        <p class="muted">Вход в панель управления.</p>
        <form class="form" method="POST" action="{{ route('dashboard.login.store') }}">
            @csrf
            @if($errors->any())<div class="errors">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <label>Email <input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus></label>
            <label>Пароль <input type="password" name="password" autocomplete="current-password" required></label>
            <button class="button" type="submit">Войти</button>
        </form>
    </main>
</body>
</html>

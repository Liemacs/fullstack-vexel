<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Контракты / Vexel Admin</title>
    <style>
        body{margin:0;background:#f5f7fb;color:#111827;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}a{text-decoration:none;color:inherit}.sidebar{position:fixed;inset:0 auto 0 0;width:264px;background:#111827;color:#f9fafb;display:flex;flex-direction:column}.brand{padding:24px;border-bottom:1px solid rgba(255,255,255,.08)}.brand strong{display:block}.brand span,.nav small,.footer{color:#9ca3af}.nav{display:grid;gap:6px;padding:18px 14px}.nav a{display:grid;gap:2px;padding:12px 14px;border-radius:8px}.nav a:hover,.nav a.active{background:#1f2937}.nav span{font-weight:800}.nav small{font-size:.76rem}.footer{margin-top:auto;padding:18px 24px;border-top:1px solid rgba(255,255,255,.08);font-size:.82rem}.content{margin-left:264px;min-height:100vh;padding:28px}.topbar,.actions{display:flex;align-items:center;justify-content:space-between;gap:16px}h1,h2,p{margin:0}.muted{color:#6b7280}.button,.secondary,.danger{display:inline-flex;min-height:38px;align-items:center;justify-content:center;padding:0 14px;border-radius:8px;font-weight:800;border:0;cursor:pointer}.button{background:#2563eb;color:#fff}.secondary{background:#fff;border:1px solid #e5e7eb}.danger{background:#fee2e2;color:#dc2626}.panel{margin-top:24px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden}.notice{margin-top:16px;padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:8px;font-weight:800}table{width:100%;border-collapse:collapse}th,td{padding:15px 18px;border-bottom:1px solid #e5e7eb;text-align:left;vertical-align:top}th{font-size:.76rem;color:#6b7280;text-transform:uppercase}.status{display:inline-flex;padding:5px 8px;border-radius:999px;background:#eef2ff;color:#3730a3;font-weight:800;font-size:.78rem}.empty{padding:40px;text-align:center}@media(max-width:1100px){table{display:block;overflow-x:auto;white-space:nowrap}}@media(max-width:900px){.sidebar{position:static;width:100%}.content{margin-left:0}.topbar{align-items:flex-start;flex-direction:column}}
    </style>
</head>
<body>
<aside class="sidebar"><div class="brand"><strong>Vexel Admin</strong><span>Панель backend</span></div><nav class="nav">@foreach($navigation as $item)<a class="{{ $item['key']==='contracts'?'active':'' }}" href="{{ $item['href'] }}"><span>{{ $item['label'] }}</span><small>{{ $item['description'] }}</small></a>@endforeach</nav><div class="footer">Laravel backend / готово для CRUD</div></aside>
<main class="content">
    <header class="topbar"><div><h1>Контракты</h1><p class="muted">Создание, редактирование и удаление контрактов для публичной страницы.</p></div><a class="button" href="{{ route('dashboard.contracts.create') }}">Создать контракт</a></header>
    @if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
    <section class="panel">
        @if($contracts->isEmpty())
            <div class="empty"><h2>Контрактов пока нет</h2><p class="muted">Создайте первый контракт, чтобы он появился на сайте.</p></div>
        @else
            <table><thead><tr><th>Номер</th><th>Название</th><th>Статус</th><th>Оплата</th><th>Клиент</th><th>Описание</th><th>Действия</th></tr></thead><tbody>
                @foreach($contracts as $contract)
                    <tr>
                        <td><strong>{{ $contract->number }}</strong></td>
                        <td>{{ $contract->title }}</td>
                        <td><span class="status">{{ $contract->status }}</span></td>
                        <td>{{ $contract->payment }}</td>
                        <td>{{ $contract->client }}</td>
                        <td>{{ Str::limit($contract->text, 90) }}</td>
                        <td><div class="actions"><a class="secondary" href="{{ route('dashboard.contracts.edit',$contract) }}">Редактировать</a><form method="POST" action="{{ route('dashboard.contracts.destroy',$contract) }}">@csrf @method('DELETE')<button class="danger" type="submit">Удалить</button></form></div></td>
                    </tr>
                @endforeach
            </tbody></table>
        @endif
    </section>
</main>
</body>
</html>

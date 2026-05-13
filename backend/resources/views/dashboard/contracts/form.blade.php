<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $contract ? 'Редактировать контракт' : 'Создать контракт' }} / Vexel Admin</title>
    <style>
        body{margin:0;background:#f5f7fb;color:#111827;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}a{text-decoration:none;color:inherit}.sidebar{position:fixed;inset:0 auto 0 0;width:264px;background:#111827;color:#f9fafb;display:flex;flex-direction:column}.brand{padding:24px;border-bottom:1px solid rgba(255,255,255,.08)}.brand span,.nav small,.footer{color:#9ca3af}.nav{display:grid;gap:6px;padding:18px 14px}.nav a{display:grid;gap:2px;padding:12px 14px;border-radius:8px}.nav a:hover,.nav a.active{background:#1f2937}.nav span{font-weight:800}.nav small{font-size:.76rem}.footer{margin-top:auto;padding:18px 24px;border-top:1px solid rgba(255,255,255,.08);font-size:.82rem}.content{margin-left:264px;min-height:100vh;padding:28px}.topbar,.actions{display:flex;align-items:center;justify-content:space-between;gap:16px}h1,p{margin:0}.muted{color:#6b7280}.button,.secondary{display:inline-flex;min-height:40px;align-items:center;justify-content:center;padding:0 14px;border-radius:8px;font-weight:800;border:0;cursor:pointer}.button{background:#2563eb;color:#fff}.secondary{background:#fff;border:1px solid #e5e7eb}.panel{margin-top:24px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:22px}.grid{display:grid;gap:18px}.fields{display:grid;gap:18px;grid-template-columns:repeat(2,minmax(0,1fr))}label{display:grid;gap:8px;color:#6b7280;font-weight:800}input,textarea,select{min-height:42px;border:1px solid #e5e7eb;border-radius:8px;padding:0 12px;font:inherit}textarea{min-height:150px;padding:12px;resize:vertical}.wide{grid-column:1/-1}.errors{padding:14px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;border-radius:8px}@media(max-width:900px){.sidebar{position:static;width:100%}.content{margin-left:0}.topbar{align-items:flex-start;flex-direction:column}.fields{grid-template-columns:1fr}}
    </style>
</head>
<body>
<aside class="sidebar"><div class="brand"><strong>Vexel Admin</strong><span>Панель backend</span></div><nav class="nav">@foreach($navigation as $item)<a class="{{ $item['key']==='contracts'?'active':'' }}" href="{{ $item['href'] }}"><span>{{ $item['label'] }}</span><small>{{ $item['description'] }}</small></a>@endforeach</nav><div class="footer">Laravel backend / готово для CRUD</div></aside>
<main class="content">
    <header class="topbar"><div><h1>{{ $contract ? 'Редактировать контракт' : 'Создать контракт' }}</h1><p class="muted">Номер, статус, оплата, клиент и текст карточки.</p></div><a class="secondary" href="{{ route('dashboard.contracts.index') }}">Назад</a></header>
    <form class="panel grid" method="POST" action="{{ $action }}">
        @csrf @if($method !== 'POST') @method($method) @endif
        @if($errors->any())<div class="errors">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <div class="fields">
            <label>Номер <input name="number" value="{{ old('number',$contract?->number) }}" placeholder="#019" required></label>
            <label>Название <input name="title" value="{{ old('title',$contract?->title) }}" placeholder="Охрана каравана" required></label>
            <label>Статус <input name="status" value="{{ old('status',$contract?->status ?? 'Активно') }}" list="contract-statuses" required></label>
            <label>Оплата <input name="payment" value="{{ old('payment',$contract?->payment) }}" placeholder="медикаменты" required></label>
            <label class="wide">Клиент <input name="client" value="{{ old('client',$contract?->client) }}" placeholder="Северный рынок" required></label>
            <label class="wide">Описание <textarea name="text" required>{{ old('text',$contract?->text) }}</textarea></label>
        </div>
        <datalist id="contract-statuses">
            <option value="Активно">
            <option value="Выполнено">
            <option value="Провалено">
            <option value="Закрыта">
            <option value="Контроль">
        </datalist>
        <div class="actions"><button class="button" type="submit">{{ $contract ? 'Сохранить' : 'Создать' }}</button><a class="secondary" href="{{ route('dashboard.contracts.index') }}">Отмена</a></div>
    </form>
</main>
</body>
</html>

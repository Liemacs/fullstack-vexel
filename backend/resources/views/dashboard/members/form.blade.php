<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $member ? 'Редактировать участника' : 'Создать участника' }} / Vexel Admin</title>
    <style>
        body{margin:0;background:#f5f7fb;color:#111827;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}a{text-decoration:none;color:inherit}.sidebar{position:fixed;inset:0 auto 0 0;width:264px;background:#111827;color:#f9fafb;display:flex;flex-direction:column}.brand{padding:24px;border-bottom:1px solid rgba(255,255,255,.08)}.brand span,.nav small,.footer{color:#9ca3af}.nav{display:grid;gap:6px;padding:18px 14px}.nav a{display:grid;gap:2px;padding:12px 14px;border-radius:8px}.nav a:hover,.nav a.active{background:#1f2937}.nav span{font-weight:800}.nav small{font-size:.76rem}.footer{margin-top:auto;padding:18px 24px;border-top:1px solid rgba(255,255,255,.08);font-size:.82rem}.content{margin-left:264px;min-height:100vh;padding:28px}.topbar,.actions{display:flex;align-items:center;justify-content:space-between;gap:16px}h1,h2,p{margin:0}.muted{color:#6b7280}.button,.secondary{display:inline-flex;min-height:40px;align-items:center;justify-content:center;padding:0 14px;border-radius:8px;font-weight:800;border:0;cursor:pointer}.button{background:#2563eb;color:#fff}.secondary{background:#fff;border:1px solid #e5e7eb}.panel{margin-top:24px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:22px}.grid{display:grid;gap:18px}.cols{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.cols2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}label{display:grid;gap:8px;color:#6b7280;font-weight:800}input,textarea,select{border:1px solid #e5e7eb;border-radius:8px;padding:0 12px;font:inherit;background:#fff}input,select{min-height:42px}textarea{min-height:110px;padding:12px;resize:vertical}.errors{padding:14px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;border-radius:8px}.preview{width:84px;height:84px;object-fit:cover;border-radius:10px;background:#e5e7eb}@media(max-width:900px){.sidebar{position:static;width:100%}.content{margin-left:0}.topbar{align-items:flex-start;flex-direction:column}.cols,.cols2{grid-template-columns:1fr}}
    </style>
</head>
<body>
<aside class="sidebar"><div class="brand"><strong>Vexel Admin</strong><span>Панель backend</span></div><nav class="nav">@foreach($navigation as $item)<a class="{{ $item['key']==='members'?'active':'' }}" href="{{ $item['href'] }}"><span>{{ $item['label'] }}</span><small>{{ $item['description'] }}</small></a>@endforeach</nav><div class="footer">Laravel backend / готово для CRUD</div></aside>
<main class="content">
    <header class="topbar"><div><h1>{{ $member ? 'Редактировать участника' : 'Создать участника' }}</h1><p class="muted">Досье участника для публичной страницы.</p></div><a class="secondary" href="{{ route('dashboard.members.index') }}">Назад</a></header>
    <form class="panel grid" method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf @if($method !== 'POST') @method($method) @endif
        @if($errors->any())<div class="errors">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <div class="cols"><label>Никнейм <input name="nickname" value="{{ old('nickname',$member?->nickname) }}" required></label><label>Пароль <input type="password" name="password" minlength="6" {{ $member ? '' : 'required' }} placeholder="{{ $member ? 'Оставьте пустым, чтобы не менять' : '' }}"></label><label>Имя <input name="name" value="{{ old('name',$member?->name) }}" required></label></div>
        <div class="cols"><label>Позывной <input name="call_sign" value="{{ old('call_sign',$member?->call_sign) }}"></label><label>Возраст <input type="number" min="0" max="255" name="age" value="{{ old('age',$member?->age) }}"></label><label>Статус <input name="status" value="{{ old('status',$member?->status) }}"></label></div>
        <label>Должность <select name="position_id"><option value="">Без должности</option>@foreach($positions as $position)<option value="{{ $position->id }}" @selected((string)old('position_id',$member?->position_id)===(string)$position->id)>{{ $position->name }}</option>@endforeach</select></label>
        <label>Роль fallback <input name="role" value="{{ old('role',$member?->role) }}" placeholder="Используется если должность не выбрана"></label>
        <label>Специализация <input name="specialization" value="{{ old('specialization',$member?->specialization) }}"></label>
        <label>Цитата <textarea name="quote">{{ old('quote',$member?->quote) }}</textarea></label>
        <label>Биография <textarea name="bio">{{ old('bio',$member?->bio) }}</textarea></label>
        <div class="cols2"><label>Характер <textarea name="character">{{ old('character',$member?->character) }}</textarea></label><label>История в Векселе <textarea name="vexel_history">{{ old('vexel_history',$member?->vexel_history) }}</textarea></label></div>
        <div class="cols"><label>Навыки, по одному в строке: Название|80 <textarea name="skills_text">{{ old('skills_text', collect($member?->skills ?? [])->map(fn($skill) => ($skill['name'] ?? '').'|'.($skill['value'] ?? 0))->implode("\n")) }}</textarea></label><label>Снаряжение, по одному в строке <textarea name="gear_text">{{ old('gear_text', implode("\n",$member?->gear ?? [])) }}</textarea></label><label>Связи, по одному в строке <textarea name="connections_text">{{ old('connections_text', implode("\n",$member?->connections ?? [])) }}</textarea></label></div>
        <label>Изображение <input type="file" name="image" accept="image/*"></label>
        @if($member?->image)<img class="preview" src="{{ $member->image }}" alt="">@endif
        <div class="actions"><button class="button" type="submit">{{ $member ? 'Сохранить' : 'Создать' }}</button><a class="secondary" href="{{ route('dashboard.members.index') }}">Отмена</a></div>
    </form>
</main>
</body>
</html>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event ? 'Редактировать хронологию' : 'Создать хронологию' }} / Vexel Admin</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f5f7fb;
            --sidebar: #111827;
            --sidebar-soft: #1f2937;
            --text: #111827;
            --muted: #6b7280;
            --panel: #ffffff;
            --line: #e5e7eb;
            --accent: #2563eb;
            --danger: #dc2626;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 264px;
            display: flex;
            flex-direction: column;
            background: var(--sidebar);
            color: #f9fafb;
        }

        .brand {
            padding: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .brand strong {
            display: block;
            font-size: 1.1rem;
        }

        .brand span,
        .nav small,
        .sidebar-footer {
            color: #9ca3af;
        }

        .nav {
            display: grid;
            gap: 6px;
            padding: 18px 14px;
        }

        .nav a {
            display: grid;
            gap: 2px;
            padding: 12px 14px;
            border-radius: 8px;
        }

        .nav a:hover,
        .nav a.active {
            background: var(--sidebar-soft);
        }

        .nav span {
            font-weight: 800;
        }

        .nav small {
            font-size: .76rem;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 18px 24px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            font-size: .82rem;
        }

        .content {
            min-height: 100vh;
            margin-left: 264px;
            padding: 28px;
        }

        .topbar,
        .form-actions,
        .chapter-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar {
            margin-bottom: 24px;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        h1 {
            font-size: 1.75rem;
        }

        .muted {
            color: var(--muted);
        }

        .button,
        .button-secondary {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
            border-radius: 8px;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
        }

        .button {
            border: 0;
            background: var(--accent);
            color: #ffffff;
        }

        .button-secondary {
            border: 1px solid var(--line);
            background: #ffffff;
            color: var(--text);
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        }

        .form-grid {
            display: grid;
            gap: 18px;
            padding: 22px;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 140px minmax(0, 1fr) 120px;
            gap: 14px;
        }

        label {
            display: grid;
            gap: 8px;
            color: var(--muted);
            font-size: .84rem;
            font-weight: 800;
        }

        input,
        textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            color: var(--text);
            font: inherit;
        }

        input {
            min-height: 42px;
            padding: 0 12px;
        }

        textarea {
            min-height: 112px;
            resize: vertical;
            padding: 12px;
            line-height: 1.5;
        }

        .chapters {
            display: grid;
            gap: 14px;
        }

        .chapter {
            display: grid;
            gap: 14px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #f9fafb;
        }

        .chapter-fields {
            display: grid;
            grid-template-columns: 120px minmax(0, 1fr);
            gap: 14px;
        }

        .errors {
            display: grid;
            gap: 6px;
            padding: 14px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            background: #fef2f2;
            color: var(--danger);
            font-weight: 700;
        }

        .form-actions {
            padding-top: 4px;
            justify-content: flex-start;
        }

        @media (max-width: 920px) {
            .sidebar {
                position: static;
                width: 100%;
            }

            .content {
                margin-left: 0;
                padding: 20px;
            }

            .topbar,
            .field-grid,
            .chapter-fields {
                align-items: flex-start;
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar" aria-label="Административная навигация">
        <div class="brand">
            <strong>Vexel Admin</strong>
            <span>Панель backend</span>
        </div>

        <nav class="nav">
            @foreach ($navigation as $item)
                <a class="{{ $item['key'] === 'timeline' ? 'active' : '' }}" href="{{ $item['href'] }}">
                    <span>{{ $item['label'] }}</span>
                    @if ($item['description'])
                        <small>{{ $item['description'] }}</small>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="sidebar-footer">Laravel backend / готово для CRUD</div>
    </aside>

    <main class="content">
        <header class="topbar">
            <div>
                <h1>{{ $event ? 'Редактировать событие' : 'Создать событие' }}</h1>
                <p class="muted">Заполните год, заголовок, краткий текст и главы для страницы истории.</p>
            </div>
            <a class="button-secondary" href="{{ route('dashboard.timeline.index') }}">Назад к списку</a>
        </header>

        <section class="panel">
            <form method="POST" action="{{ $action }}" class="form-grid">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                @if ($errors->any())
                    <div class="errors">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="field-grid">
                    <label>
                        Год
                        <input name="year" value="{{ old('year', $event?->year) }}" placeholder="2039" required>
                    </label>
                    <label>
                        Заголовок
                        <input name="title" value="{{ old('title', $event?->title) }}" placeholder="Основание Векселя" required>
                    </label>
                    <label>
                        Порядок
                        <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $event?->sort_order ?? 0) }}">
                    </label>
                </div>

                <label>
                    Краткое описание
                    <textarea name="text" required placeholder="Название закрепилось в журналах контрактов и радиопозывных.">{{ old('text', $event?->text) }}</textarea>
                </label>

                <div class="chapters">
                    <div class="chapter-head">
                        <div>
                            <h2>Главы</h2>
                            <p class="muted">Пустые главы будут проигнорированы. Чтобы удалить главу при редактировании, очистите ее поля.</p>
                        </div>
                    </div>

                    @foreach (old('chapters', $chapters) as $index => $chapter)
                        <div class="chapter">
                            <h3>Глава {{ $index + 1 }}</h3>
                            <div class="chapter-fields">
                                <label>
                                    Номер
                                    <input name="chapters[{{ $index }}][chapter]" value="{{ $chapter['chapter'] ?? '' }}" placeholder="01">
                                </label>
                                <label>
                                    Заголовок главы
                                    <input name="chapters[{{ $index }}][title]" value="{{ $chapter['title'] ?? '' }}" placeholder="Имя в журнале">
                                </label>
                            </div>
                            <label>
                                Текст главы
                                <textarea name="chapters[{{ $index }}][text]" placeholder="Текст главы для страницы истории.">{{ $chapter['text'] ?? '' }}</textarea>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="form-actions">
                    <button class="button" type="submit">{{ $event ? 'Сохранить изменения' : 'Создать событие' }}</button>
                    <a class="button-secondary" href="{{ route('dashboard.timeline.index') }}">Отмена</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Хронология / Vexel Admin</title>
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
            --success: #15803d;
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
        .row-actions,
        .panel-head {
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
        .button-secondary,
        .button-danger {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
            border: 0;
            border-radius: 8px;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
        }

        .button {
            background: var(--accent);
            color: #ffffff;
        }

        .button-secondary {
            border: 1px solid var(--line);
            background: #ffffff;
            color: var(--text);
        }

        .button-danger {
            background: #fee2e2;
            color: var(--danger);
        }

        .panel {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        }

        .panel-head {
            padding: 20px 22px;
            border-bottom: 1px solid var(--line);
        }

        .notice {
            margin-bottom: 16px;
            padding: 12px 14px;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            background: #f0fdf4;
            color: var(--success);
            font-weight: 800;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 820px;
        }

        th,
        td {
            padding: 15px 18px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: top;
        }

        th {
            color: var(--muted);
            font-size: .76rem;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .year {
            font-weight: 900;
        }

        .empty {
            display: grid;
            min-height: 260px;
            place-items: center;
            padding: 32px;
            text-align: center;
        }

        .empty div {
            max-width: 520px;
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
            .panel-head {
                align-items: flex-start;
                flex-direction: column;
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
                <h1>Хронология</h1>
                <p class="muted">События и главы, которые отображаются на странице истории.</p>
            </div>
            <a class="button" href="{{ route('dashboard.timeline.create') }}">Создать событие</a>
        </header>

        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>События</h2>
                    <p class="muted">Порядок сортировки управляет очередностью на странице истории.</p>
                </div>
            </div>

            @if ($events->isEmpty())
                <div class="empty">
                    <div>
                        <h2>Событий пока нет</h2>
                        <p class="muted">Создайте первое событие хронологии, добавьте год, краткое описание и главы.</p>
                    </div>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Порядок</th>
                                <th>Год</th>
                                <th>Событие</th>
                                <th>Главы</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $event)
                                <tr>
                                    <td>{{ $event->sort_order }}</td>
                                    <td class="year">{{ $event->year }}</td>
                                    <td>
                                        <strong>{{ $event->title }}</strong>
                                        <p class="muted">{{ $event->text }}</p>
                                    </td>
                                    <td>{{ $event->chapters->count() }}</td>
                                    <td>
                                        <div class="row-actions">
                                            <a class="button-secondary" href="{{ route('dashboard.timeline.edit', $event) }}">Редактировать</a>
                                            <form method="POST" action="{{ route('dashboard.timeline.destroy', $event) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="button-danger" type="submit">Удалить</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </main>
</body>
</html>

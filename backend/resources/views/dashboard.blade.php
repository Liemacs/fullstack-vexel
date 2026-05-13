<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Панель управления</title>
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

        .brand span {
            display: block;
            margin-top: 4px;
            font-size: .85rem;
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

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 24px;
        }

        h1,
        h2,
        p {
            margin: 0;
        }

        h1 {
            font-size: 1.75rem;
            letter-spacing: 0;
        }

        .muted {
            color: var(--muted);
        }

        .button {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
            border-radius: 8px;
            background: var(--accent);
            color: #ffffff;
            font-weight: 800;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .card,
        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        }

        .card {
            padding: 18px;
        }

        .card span {
            color: var(--muted);
            font-size: .82rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .card strong {
            display: block;
            margin: 10px 0 6px;
            font-size: 2rem;
        }

        .panel {
            padding: 22px;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--line);
        }

        .empty {
            display: grid;
            min-height: 220px;
            place-items: center;
            padding: 28px;
            text-align: center;
        }

        .empty-inner {
            max-width: 520px;
        }

        .empty h2 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 20px;
        }

        .quick-link {
            display: grid;
            gap: 6px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #f9fafb;
        }

        .quick-link strong {
            color: var(--text);
        }

        .quick-link span {
            color: var(--muted);
            font-size: .9rem;
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

            .stats,
            .quick-grid {
                grid-template-columns: 1fr;
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
                <a class="{{ $item['key'] === 'dashboard' ? 'active' : '' }}" href="{{ $item['href'] }}">
                    <span>{{ $item['label'] }}</span>
                    <small>{{ $item['description'] }}</small>
                </a>
            @endforeach
        </nav>

        <div class="sidebar-footer">
            Laravel backend / готово для CRUD
        </div>
    </aside>

    <main class="content">
        <header class="topbar">
            <div>
                <h1>Панель управления</h1>
                <p class="muted">Административная панель, подготовленная для CRUD-модулей.</p>
            </div>
            <a class="button" href="{{ route('dashboard.health') }}">Статус API</a>
        </header>

        <section class="stats" aria-label="Статистика">
            @foreach ($stats as $stat)
                <article class="card">
                    <span>{{ $stat['label'] }}</span>
                    <strong>{{ $stat['value'] }}</strong>
                    <p class="muted">{{ $stat['hint'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>Данных пока нет</h2>
                    <p class="muted">Тестовые данные удалены. Следующий шаг — добавить CRUD для каждого модуля.</p>
                </div>
            </div>

            <div class="empty">
                <div class="empty-inner">
                    <h2>Чистая административная панель</h2>
                    <p class="muted">Левое меню содержит основные страницы. Каждая страница сейчас имеет placeholder и готова принять списки, формы создания, редактирования и удаления.</p>

                    <div class="quick-grid">
                        @foreach (array_slice($navigation, 1, 6) as $item)
                            <a class="quick-link" href="{{ $item['href'] }}">
                                <strong>{{ $item['label'] }}</strong>
                                <span>{{ $item['description'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

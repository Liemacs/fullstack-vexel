<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $current['label'] }} / Vexel Admin</title>
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
        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .topbar {
            margin-bottom: 24px;
        }

        h1,
        h2,
        p {
            margin: 0;
        }

        h1 {
            font-size: 1.75rem;
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

        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        }

        .panel-head {
            padding: 22px;
            border-bottom: 1px solid var(--line);
        }

        .placeholder {
            display: grid;
            min-height: 340px;
            place-items: center;
            padding: 32px;
            text-align: center;
        }

        .placeholder div {
            max-width: 560px;
        }

        .placeholder h2 {
            margin-bottom: 10px;
            font-size: 1.25rem;
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
                <a class="{{ $item['key'] === $current['key'] ? 'active' : '' }}" href="{{ $item['href'] }}">
                    <span>{{ $item['label'] }}</span>
                    @if ($item['description'])
                        <small>{{ $item['description'] }}</small>
                    @endif
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
                <h1>{{ $current['label'] }}</h1>
                <p class="muted">{{ $current['description'] }}</p>
            </div>
            <a class="button" href="{{ route('dashboard') }}">Назад к панели</a>
        </header>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>{{ $current['label'] }}</h2>
                    <p class="muted">Страница подготовлена для CRUD.</p>
                </div>
            </div>

            <div class="placeholder">
                <div>
                    <h2>Пустой модуль</h2>
                    <p class="muted">Здесь появятся список, форма создания, редактирование и удаление для раздела «{{ mb_strtolower($current['label']) }}».</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

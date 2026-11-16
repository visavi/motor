<?php
/** @var string $title */
/** @var string $content */
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= htmlspecialchars($title) ?> — Motor CMS</title>
    <style>
        :root {
            --bg: #f2f0ec;
            --surface: #ffffff;
            --text: #191b1f;
            --muted: #565b63;
            --border: #e0ddd6;
            --primary: #1e5f52;
            --danger: #b42318;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #14181a;
                --surface: #1b201f;
                --text: #e3e6e2;
                --muted: #949c98;
                --border: #29302f;
                --primary: #6ab8a4;
                --danger: #f2877d;
            }
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: var(--bg);
            color: var(--text);
            font: 16px/1.6 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        main {
            width: 100%;
            max-width: 560px;
            padding: 32px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
        }
        h1 { margin: 0 0 16px; font-size: 24px; line-height: 1.3; }
        p, ol, ul { margin: 0 0 16px; }
        li + li { margin-top: 8px; }
        a { color: var(--primary); }
        code {
            padding: 2px 6px;
            background: var(--bg);
            border-radius: 4px;
            font-size: 14px;
            overflow-wrap: anywhere;
        }
        .muted { color: var(--muted); font-size: 14px; margin-bottom: 0; }
        .danger { color: var(--danger); }
        .button {
            display: inline-block;
            padding: 8px 16px;
            background: var(--primary);
            color: var(--surface);
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>
<main>
    <?= $content ?>
</main>
</body>
</html>

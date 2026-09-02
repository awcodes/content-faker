<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Content Faker Workbench' }}</title>
    <style>
        :root { color-scheme: light dark; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 2rem 1.5rem 4rem;
            font: 16px/1.6 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 60rem;
            margin-inline: auto;
        }
        header { border-bottom: 1px solid rgb(128 128 128 / 0.3); padding-bottom: 1rem; margin-bottom: 2rem; }
        header h1 { margin: 0 0 0.25rem; font-size: 1.25rem; }
        header p { margin: 0; opacity: 0.7; font-size: 0.875rem; }
        a { color: inherit; }
        section { margin-bottom: 3rem; }
        section > h2 { font-size: 0.75rem; letter-spacing: 0.08em; text-transform: uppercase; opacity: 0.6; }
        .rendered, pre {
            border: 1px solid rgb(128 128 128 / 0.3);
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
        }
        pre { overflow-x: auto; font-size: 0.8125rem; white-space: pre-wrap; word-break: break-word; }
        .rendered img { max-width: 100%; height: auto; }
        .rendered table { border-collapse: collapse; }
        .rendered th, .rendered td { border: 1px solid rgb(128 128 128 / 0.3); padding: 0.35rem 0.6rem; }
        .rendered .button { display: inline-block; padding: 0.4rem 0.9rem; border: 1px solid currentColor; border-radius: 0.35rem; text-decoration: none; }
        .rendered .columns { display: grid; grid-template-columns: repeat(auto-fit, minmax(14rem, 1fr)); gap: 1rem; }
        .rendered .callout, .rendered .filament-block { border-left: 3px solid currentColor; padding-left: 1rem; opacity: 0.9; }
        ul.posts { list-style: none; padding: 0; }
        ul.posts li { padding: 0.5rem 0; border-bottom: 1px solid rgb(128 128 128 / 0.2); }
    </style>
</head>
<body>
<header>
    <h1><a href="{{ route('posts.index') }}">Content Faker Workbench</a></h1>
    <p>A minimal consuming application for <code>awcodes/content-faker</code>.</p>
</header>

@yield('content')
</body>
</html>

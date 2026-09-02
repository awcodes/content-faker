@extends('layouts.app', ['title' => $post->title])

@section('content')
    <h2 style="font-size: 1.75rem; text-transform: none; letter-spacing: 0; opacity: 1;">{{ $post->title }}</h2>

    {{-- The rendered panes intentionally output the generated content unescaped: seeing
         the markup a faker produces is the whole point of this Workbench. The fakers
         escape their own text and attribute values. --}}

    <section>
        <h2>Markdown &mdash; rendered</h2>
        <div class="rendered">{!! Str::markdown($post->markdown_content) !!}</div>

        <h2>Markdown &mdash; source</h2>
        <pre>{{ $post->markdown_content }}</pre>
    </section>

    <section>
        <h2>HTML &mdash; rendered</h2>
        <div class="rendered">{!! $post->html_content !!}</div>

        <h2>HTML &mdash; source</h2>
        <pre>{{ $post->html_content }}</pre>
    </section>

    <section>
        <h2>Rich editor &mdash; rendered</h2>
        <div class="rendered">{!! $post->rich_content !!}</div>

        <h2>Rich editor &mdash; source</h2>
        <pre>{{ $post->rich_content }}</pre>
    </section>
@endsection

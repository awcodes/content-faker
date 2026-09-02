@extends('layouts.app')

@section('content')
    <p>
        Each post below was created by <code>Workbench\Database\Factories\PostFactory</code>,
        which fills one column per generator: Markdown, HTML and rich editor.
    </p>

    <ul class="posts">
        @forelse ($posts as $post)
            <li>
                <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
            </li>
        @empty
            <li>No posts have been seeded. Run <code>composer build</code>.</li>
        @endforelse
    </ul>
@endsection

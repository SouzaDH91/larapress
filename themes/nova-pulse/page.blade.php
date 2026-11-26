@extends('theme::layout')

@section('title', $page->title . ' - ' . get_option('site_name', 'NovaPulse'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <article class="np-fade-in">
                <header class="mb-4 pb-3 border-bottom">
                    <h1 class="np-article-title mb-2">{{ $page->title }}</h1>
                    <div class="text-muted small">
                        <span>Página Institucional</span>
                    </div>
                </header>

                <div class="np-content">
                    {!! do_shortcode($page->content_html ?? '') !!}
                </div>
            </article>
        </div>
    </div>
</div>
@endsection

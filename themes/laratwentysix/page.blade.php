@extends('theme::layout')

@section('title', $page->title . ' - ' . get_option('site_name'))

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="bg-white p-4 p-md-5 rounded shadow-sm border">
                <h1 class="fw-bold mb-4">{{ $page->title }}</h1>
                <div class="page-content fs-5 lh-lg">
                    {!! do_shortcode($page->content_html ?? '') !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

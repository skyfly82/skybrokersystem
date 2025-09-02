@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'noindex' => false,
    'structuredData' => null
])

@php
    $seoTitle = $title ?: (config('app.name') . ' - ' . __('common.professional_courier') . ' ' . __('common.brokerage_platform'));
    $seoDescription = $description ?: __('common.hero_description');
    $seoImage = $image ?: asset('images/og-image.jpg');
    $currentUrl = request()->url();
    $canonicalUrl = $canonical ?: $currentUrl;
@endphp

<!-- SEO Meta Tags -->
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
@if($keywords)
<meta name="keywords" content="{{ $keywords }}">
@endif
<meta name="author" content="SkyBrokerSystem">
<meta name="robots" content="{{ $noindex ? 'noindex, nofollow' : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1' }}">

<!-- Language and Regional -->
<meta name="language" content="{{ app()->getLocale() }}">
<link rel="alternate" hreflang="pl" href="{{ str_replace(request()->path(), 'language/pl', $currentUrl) }}">
<link rel="alternate" hreflang="en" href="{{ str_replace(request()->path(), 'language/en', $currentUrl) }}">
<link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

<!-- Canonical URL -->
<link rel="canonical" href="{{ $canonicalUrl }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $currentUrl }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
<meta property="og:site_name" content="SkyBrokerSystem">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $currentUrl }}">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<!-- Additional SEO -->
<meta name="theme-color" content="#4f46e5">
<meta name="msapplication-TileColor" content="#4f46e5">

@if($structuredData)
<!-- Structured Data -->
<script type="application/ld+json">{!! $structuredData !!}</script>
@endif
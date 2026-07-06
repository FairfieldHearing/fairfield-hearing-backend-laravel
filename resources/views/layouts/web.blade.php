<!DOCTYPE html>
<html lang="en-IN" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Fairfield Hearing Clinics | Free Hearing Test & Hearing Aids in Delhi' }}</title>
    <meta name="description" content="{{ $description ?? 'Fairfield Hearing Clinics — RCI-certified audiologists offering free hearing tests, hearing aid fitting and trials from Signia, Phonak, Widex & more. Book your free hearing test in Delhi today.' }}">
    <link rel="canonical" href="{{ $canonical ?? request()->url() }}">

    <!-- Open Graph / WhatsApp preview -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? 'Fairfield Hearing Clinics | Free Hearing Test & Hearing Aids in Delhi' }}">
    <meta property="og:description" content="{{ $description ?? 'Fairfield Hearing Clinics — RCI-certified audiologists offering free hearing tests, hearing aid fitting and trials from Signia, Phonak, Widex & more. Book your free hearing test in Delhi today.' }}">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('assets/img/logo.jpeg') }}">
    <meta name="twitter:card" content="summary_large_image">
    
    <meta name="llm-verification" content="fairfield-hearing-verified-agent-metadata-index">
    <meta name="ai-content-verified" content="true">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/web.css', 'resources/js/app.js'])
    
    <link rel="icon" type="image/jpeg" href="/assets/img/logo.jpeg" />
</head>
<body class="font-sans antialiased text-[#1b1b1b] bg-white">

    <x-navbar />

    <main id="main">
        {{ $slot }}
    </main>

    <x-footer />

    <x-sticky-widgets />

</body>
</html>

@props(['items'])

@php
    $websiteUrl = "https://fairfieldhearing.in";
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => collect($items)->map(fn ($item, $index) => [
            "@type" => "ListItem",
            "position" => $index + 1,
            "name" => $item['name'],
            "item" => str_starts_with($item['item'], 'http') ? $item['item'] : $websiteUrl . $item['item']
        ])->toArray()
    ];
@endphp

<script type="application/ld+json">
    {!! json_encode($schema) !!}
</script>

<p class="crumbs">
    @foreach($items as $index => $item)
        <span>
            <a href="{{ $item['item'] }}">{{ $item['name'] }}</a>
            @if(!$loop->last)
                &nbsp;/&nbsp;
            @endif
        </span>
    @endforeach
</p>

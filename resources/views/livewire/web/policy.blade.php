<div>
    <script type="application/ld+json">
        {!! json_encode($policySchema) !!}
    </script>

    <main id="main">
        <section class="section">
            <div class="container">
                <div class="article">
                    <h1>{{ $policy['title'] }}</h1>

                    <div class="fhc-article-content-body">
                        {!! $policyContent !!}
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<!-- Gig Section -->
<section class="gig-section job-section">
    <div class="auto-container">
        @if($title || $sub_title)
            <div class="sec-title text-center">
                @if($title)
                    <h2>{{ $title }}</h2>
                @endif
                @if($sub_title)
                    <div class="text">{{ $sub_title }}</div>
                @endif
            </div>
        @endif

        <div class="row mb-5 wow fadeInUp">
            @foreach($rows as $row)
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
                    @include('Gig::frontend.search.loop', ['row' => $row])
                </div>
            @endforeach
        </div>

        @if(!empty($load_more_url))
            <div class="btn-box">
                <a href="{{ $load_more_url }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">{{ __("Load More Listing") }}</span></a>
            </div>
        @endif
    </div>
</section>
<!-- End Gig Section -->

<link href="{{ asset('dist/frontend/module/gig/css/gig.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">

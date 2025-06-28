@php
    $topBrands = \App\Models\Brand::featured()->get();
    $content = getContent('featured_brands.content', true);
@endphp

<!-- <section class="my-60">
    <div class="container">
        @if (!blank($topBrands))
            <div class="section-header">
                <h5 class="title">{{ __(@$content->data_values->title) }}</h5>
            </div>

            <div class="small-card">
                @foreach ($topBrands as $brand)
                    <div class="small-card-item text-center">
                        <x-dynamic-component :component="frontendComponent('brand-card')" :brand="$brand" />
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section> -->

<style>
    .brand-slider .small-card-item {
        height: 150px; /* Adjust this value as needed */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .brand-slider .small-card-item img {
        width: 100%; 
        height: 100%; 
        object-fit: contain; 
    }

    .brand-slider .small-card-item .brand-card {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    

    /* Position the previous arrow outside of the slider */
    .owl-prev {
        position: absolute;
        left: -50px; 
        top: 50%; 
        transform: translateY(-50%); 
        z-index: 10;
    }

    /* Position the next arrow outside of the slider */
    .owl-next {
        position: absolute;
        right: -50px; 
        top: 50%; 
        transform: translateY(-50%); 
        z-index: 10;
    }

    /* Optional: Add a background color and adjust appearance */
    .owl-prev, .owl-next {
        background-color: rgba(0, 0, 0, 0.5); 
        color: white;
        padding: 12px;
        font-size: 20px;
    }

    /* Optional: Add hover effect for the arrows */
    .owl-prev:hover, .owl-next:hover {
        background-color: rgba(0, 0, 0, 0.7); /* Darker background on hover */
    }
</style>

<section class="my-60">
    <div class="container">
        @if (!blank($topBrands))
            <div class="section-header">
                <h5 class="title">{{ __(@$content->data_values->title) }}</h5>
            </div>

            <div class="brand-slider owl-theme owl-carousel">
                @foreach ($topBrands as $brand)
                    <div class="small-card-item text-center">
                        <x-dynamic-component :component="frontendComponent('brand-card')" :brand="$brand" />
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@push('script')
    <script>
        (function($) {
            "use strict";

            $(".brand-slider").owlCarousel({
                margin: 16,
                responsiveClass: true,
                items: 7,  // Show 7 brands per slide
                nav: true,  // Enable navigation arrows
                dots: false,
                autoplay: true,
                autoplayTimeout: 5000,  // Slide every 5 seconds
                loop: true,
                lazyLoad: true,
                responsive: {
                    0: {
                        items: 1,  // Show 1 item on small screens
                    },
                    425: {
                        items: 2,  // Show 2 items on medium screens
                    },
                    768: {
                        items: 4,  // Show 4 items on larger screens
                    },
                    992: {
                        items: 5,  // Show 5 items on even larger screens
                    },
                    1199: {
                        items: 8,  // Show 7 items on large screens
                    }
                },
                onInitialized: function() {
                    // Always show the arrows, even if there are fewer items
                    $(".owl-prev, .owl-next").show();
                },
                onResize: function() {
                    // Show arrows when resizing the window
                    $(".owl-prev, .owl-next").show();
                }
            });
        })(jQuery);
    </script>
@endpush

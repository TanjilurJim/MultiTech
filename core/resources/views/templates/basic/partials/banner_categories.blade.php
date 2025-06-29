@php
    $featuredCategories = \App\Models\Category::where('feature_in_banner', 1)->with('products.brand')->get();

    if (gs('homepage_layout') == 'sidebar_menu') {
        $categoriesToShow = [
            768 => ['items' => 5],
            992 => ['items' => 5],
            1199 => ['items' => 5],
        ];
        if (!$fixedBanner) {
            $categoriesToShow[1399] = ['items' => 6];
        }
    } else {
        $categoriesToShow = [
            768 => ['items' => 6],
            992 => ['items' => 7],
            1199 => ['items' => 9],
        ];
    }
@endphp
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
@if (!blank($featuredCategories))
    <div class="overflow-hidden">
        <div class="featured-category-slider owl-theme owl-carousel">
            @foreach ($featuredCategories as $category)
                <div class="single-category p-2">
                    <x-dynamic-component :component="frontendComponent('category-card')" :category="$category" />
                </div>
            @endforeach
        </div>
    </div>

    @push('script')
        <script>
            (function($) {
                "use strict";
                const categoriesToShow = @json($categoriesToShow);

                const viewItems = {
                    0: {
                        items: 3,
                        margin: 12,
                    },
                    425: {
                        items: 4,
                        margin: 12,
                    },
                    575: {
                        items: 4,
                        margin: 12,
                    },
                    ...categoriesToShow
                };



                $(".featured-category-slider").owlCarousel({
                    margin: 16,
                    responsiveClass: true,
                    items: 3,
                    nav: false,
                    dots: false,
                    autoplay: true,
                    autoplayTimeout: 4000,
                    loop: true,
                    lazyLoad: true,
                    responsive: viewItems,
                });
            })(jQuery);
        </script>
    @endpush

@endif

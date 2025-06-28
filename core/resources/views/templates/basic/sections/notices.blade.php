@php
    $notices = getContent('notices.element', orderById: true);
    

    if (gs('homepage_layout') == 'sidebar_menu') {
        $noticesToShow = [
            768 => ['items' => 1],  // Show 1 item per slide
            992 => ['items' => 1],
            1199 => ['items' => 1],
        ];
        if (!$fixedBanner) {
            $noticesToShow[1399] = ['items' => 1];
        }
    } else {
        $noticesToShow = [
            768 => ['items' => 1],  // Show 1 item per slide
            992 => ['items' => 1],
            1199 => ['items' => 1],
        ];
    }
@endphp
<style>
    .notice-card__link {
        color: #DD4637;
    }
    .notice-card__link:hover {
        color:rgb(146, 47, 38);
    }
</style>
@if($notices->count())
    <div class="overflow-hidden">
        <div class="notice-slider owl-theme owl-carousel">
            @foreach ($notices as $notice)
                <div class="single-notice container p-2">
                    <div class="notice-card">
                        <div class="notice-card__content">
                            <h5 class="notice-card__title mb-1 text-center"><a href="{{ __($notice->data_values->link) }}" class="notice-card__link">{{ __($notice->data_values->title) }}</a></h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @push('script')
        <script>
            (function($) {
                "use strict";
                const noticesToShow = @json($noticesToShow);

                const viewItems = {
                    0: {
                        items: 1,  // Show 1 item on small screens
                        margin: 12,
                    },
                    425: {
                        items: 1,  // Show 1 item on medium screens
                        margin: 12,
                    },
                    575: {
                        items: 1,  // Show 1 item on medium screens
                        margin: 12,
                    },
                    ...noticesToShow
                };

                $(".notice-slider").owlCarousel({
                    margin: 16,
                    responsiveClass: true,
                    items: 1,  // Show 1 item by default
                    nav: true,  // Enable navigation arrows
                    dots: false,
                    autoplay: true,
                    autoplayTimeout: 10000,
                    loop: true,
                    lazyLoad: true,
                    responsive: viewItems,
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
@endif

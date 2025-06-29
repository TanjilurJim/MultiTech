@php
    $headerThreeKeys = array_keys((array) $headerThree->group);

    $firstLetters = array_map(function ($key) {
        return $key[0];
    }, $headerThreeKeys);

    $layoutClass = 'primary-menu-' . implode('', $firstLetters);

    $headerColor = $headerThree?->background_color ?? gs('base_color');

    $featuredCategories = \App\Models\Category::where('feature_in_banner', 1)->with('subcategories.allSubcategories')->get();
    $mainMenuLimit = 9;
@endphp

@if (@$headerThree->status == 'on')
    <div class="header-bottom @if (gs('homepage_layout') == 'full_width_banner') without-category @endif">
        <div class="container">
            <div class="row g-0">
                <div class="header-bottom-wrapper {{ $layoutClass }}">

                    <button class="primary-menu-button d-lg-none">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>

                    <!-- <nav class="navbar navbar-expand-lg navbar-light"> -->
                    <nav class="navbar navbar-expand-lg navbar-light py-0">
                        <div class="container-fluid px-0">
                            <div class="collapse navbar-collapse" id="navbarMegaMenu">
                                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                    @foreach ($featuredCategories->take($mainMenuLimit) as $category)
                                        <li class="nav-item dropdown position-static group">
                                            <a class="nav-link main-menu-link d-flex align-items-center justify-content-between p-3" href="{{ $category->shopLink() }}">
                                                <span>{{ $category->name }}</span>
                                                @if ($category->subcategories->count())
                                                    <i class="fas fa-chevron-down ms-1 transition-transform group-hover:rotate-180"></i>
                                                @endif
                                            </a>
                                            @if ($category->subcategories->count())
                                                <div class="dropdown-menu mega-dropdown fullwidth p-3">
                                                    <div class="row">
                                                        @foreach ($category->subcategories->chunk(ceil($category->subcategories->count() / 3)) as $chunk)
                                                            <div class="col-md-4">
                                                                <ul class="list-unstyled">
                                                                    @foreach ($chunk as $subcategory)
                                                                        <li class="position-relative group">
                                                                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ $subcategory->shopLink() ?? '#' }}">
                                                                                {{ $subcategory->name }}
                                                                                @if ($subcategory->allSubcategories->count())
                                                                                    <i class="fas fa-angle-right"></i>
                                                                                @endif
                                                                            </a>
                                                                            @if ($subcategory->allSubcategories->count())
                                                                                <ul class="dropdown-menu sub-menu">
                                                                                    @foreach ($subcategory->allSubcategories as $child)
                                                                                        <li>
                                                                                            <a class="dropdown-item" href="{{ $child->shopLink() ?? '#' }}">{{ $child->name }}</a>
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach

                                    @if ($featuredCategories->count() > $mainMenuLimit)
                                        <li class="nav-item dropdown position-static">
                                            <a class="nav-link main-menu-link d-flex align-items-center justify-content-between" href="#" id="viewMoreDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>View More</span>
                                                <i class="fas fa-chevron-down ms-1"></i>
                                            </a>
                                            <ul class="dropdown-menu p-2">
                                                @foreach ($featuredCategories->slice($mainMenuLimit) as $category)
                                                    <li>
                                                        <a class="dropdown-item" href="{{ $category->shopLink() }}">{{ $category->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </nav>

                </div>
            </div>
        </div>
    </div>
@endif

@push('style')
    <style>
        .header-bottom {
            background-color: #{{ $headerColor }};
        }

        .main-menu-link {
            position: relative;
            padding: 10px 15px;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s;
        }

        .main-menu-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
        }

        .mega-dropdown.fullwidth {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #fff;
            z-index: 1050;
            width: 100%;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .nav-item.dropdown:hover > .mega-dropdown.fullwidth {
            display: block;
        }

        /* .dropdown-menu.sub-menu {
            display: none;
            position: absolute;
            top: 0;
            left: 100%;
            min-width: 220px;
            background: #fff;
            z-index: 1051;
        }

        li.position-relative:hover > .dropdown-menu.sub-menu {
            display: block;
        } */
         

        /* ...existing code... */
        .dropdown-menu.sub-menu {
            display: none;
            position: absolute;
            top: 0;
            left: 100%;
            min-width: 220px;
            background: #fff;
            z-index: 1051;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            padding: 0;
        }

        li.position-relative:hover > .dropdown-menu.sub-menu,
        li.position-relative:focus-within > .dropdown-menu.sub-menu {
            display: block;
        }

        li.position-relative {
            position: relative;
        }
        /* ...existing code... */


        .dropdown-item {
            font-size: 14px;
            color: #333;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
            color: #DA2128;
        }
    </style>
@endpush

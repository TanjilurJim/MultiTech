@php
    $productTypes = App\Models\ProductType::whereNotNull('specifications')->get();
@endphp

<div class="card mb-4">
    <div class="card-body">
        <!-- Specify Field (New) -->
        <div class="form-group row">
            <div class="col-md-3">
                <label>@lang('Specify')</label>
            </div>
            <div class="col-md-9">
                <textarea rows="5" class="form-control" name="specify" id="specifyField">{{ $product->specify ?? '' }}</textarea>
            </div>
        </div>
        <!-- <div class="select2-parent d-flex flex-wrap">
            <label class="required">@lang('Product Type')</label>
            <select class="form-control w-auto flex-grow-1" name="product_type_id">
                <option value="">@lang('Select One')</option>
                @foreach ($productTypes as $productType)
<option value="{{ @$productType->id }}" data-specifications='@json($productType->specifications)' @selected($productType->id == @$product->product_type_id)>{{ __($productType->name) }}</option>
@endforeach
            </select>
        </div> -->
    </div>
</div>
<div class="specifications-wrapper row gy-4"></div>

@push('script')
    {{-- TinyMCE core --}}
    <script src="https://unpkg.com/tinymce@5.3.0/tinymce.min.js"></script>

    {{-- TinyMCE + FontAwesome picker init --}}
    <script>
        tinymce.init({
            selector: '#specifyField', // ← only this textarea
            plugins: 'fontawesomepicker advlist autolink lists link image charmap print preview hr anchor pagebreak accordion autoresize code codesample directionality fullscreen emoticons help quickbars table',
            toolbar: 'fontawesomepicker forecolor backcolor fontsizeselect formatselect | bullist numlist | bold italic underline | link image | code',
            fontsize_formats: '12px 14px 16px 18px 24px 36px',
            external_plugins: {
                fontawesomepicker: 'https://www.unpkg.com/tinymce-fontawesomepicker/fontawesomepicker/plugin.min.js'
            },
            fontawesomeUrl: 'https://www.unpkg.com/@fortawesome/fontawesome-free@5.14.0/css/all.min.css',
            height: 300,
            menubar: false
        });
    </script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            const specificationValues = @json(@$product->specification ?? []);

            const specificationsWrapper = $('.specifications-wrapper');
            const productType = $('[name="product_type_id"]');

            const buildSpecificationGroup = (groupName) => {
                return `<div class="col-12">
                        <div class="card">
                            <div class="card-header"><h5 class="card-title">${groupName}</h5></div>
                            <div class="card-body row gy-4"></div>
                        </div>
                    </div>`;
            }

            const buildSpecificationHTML = (specification, count, value = '') => {
                return `<div class="col-sm-6 col-xxl-4">
                            <input type="hidden" name="specification[${count}][key]" value="${specification}" >
                            <label class="color--small">${specification}</label>
                            <input type="text" class="form-control" name="specification[${count}][value]" value="${value}">
                        </div>`;
            }

            const handleProductTypeChange = function() {
                let template = $(this).find(':selected');
                let specifications = template.data('specifications');
                specificationsWrapper.empty();
                if (specifications) {
                    let index = 0;
                    specifications.forEach((specification, i) => {
                        const specificationGroup = buildSpecificationGroup(specification.group_name);
                        const groupElement = appendAndShowElement($(specificationsWrapper),
                            specificationGroup, false);

                        specification.attributes.forEach((attribute => {
                            let value = '';
                            if (specificationValues.length > 0) {
                                const specificationItem = specificationValues.find(item => item
                                    .key === attribute);
                                value = specificationItem && specificationItem.value ?
                                    specificationItem.value : '';
                            }
                            const content = buildSpecificationHTML(attribute, index, value);
                            appendAndShowElement($(groupElement).find('.card-body'), content,
                                false);
                            index++;
                        }));
                    });
                }
            }

            productType.select2({
                dropdownParent: productType.parent('.select2-parent'),
            });

            productType.on('change', handleProductTypeChange).change();

        })(jQuery);
    </script>
@endpush

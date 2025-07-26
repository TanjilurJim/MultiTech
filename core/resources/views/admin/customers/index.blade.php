{{-- resources/views/admin/customers/index.blade.php --}}
@extends('admin.layouts.app')

@section('panel')
    @php
        /* ── 1. pull the raw JSON exactly once ─────────────────────────────── */
        $bd = getBangladeshLocationData();

        $postcodes = json_decode(file_get_contents(resource_path('data/bd-postcodes.json')), true)['postcodes'];

        $divName = collect($bd['divisions'])->pluck('name', 'id'); // [id ⇒ name]
        $disName = collect($bd['districts'])->pluck('name', 'id');
        $upaName = collect($bd['upazilas'])->pluck('name', 'id');
    @endphp

    {{-- ───── Page header / breadcrumb ───── --}}
    <div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center">
        <h6 class="page-title">{{ __('Customers') }}</h6>
        <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
            @stack('breadcrumb-plugins')
        </div>
        @if (session('success'))
            <div class="alert alert-success auto-hide-alert position-fixed">{{ session('success') }}</div>
        @endif
    </div>

    {{-- ───── Top button switcher ───── --}}
    <div class="btn-group mb-3" role="group" aria-label="Customer Sections">
        <a href="{{ route('admin.customers.index') }}" class="btn btn--primary active">
            @lang('Customer Database')
        </a>
        <a href="{{ route('admin.followups.index') }}" class="btn btn--light border">
            @lang('Follow-Up Report')
        </a>
    </div>

    {{-- ───── Filter row ───── --}}
    <form method="get" class="row g-2 mb-3 align-items-center">
        {{-- ───── Top action row (right aligned) ───── --}}
        <div class="d-flex justify-content-end mb-3">
            @can('customers.add')
                <button type="button" class="btn btn--sm btn--primary me-2" data-bs-toggle="modal"
                    data-bs-target="#customer-modal">
                    @lang('Add Customer')
                </button>
            @endcan
            <a href="{{ route('admin.customers.export', request()->query()) }}" class="btn btn--sm btn-outline--primary">
                @lang('Export CSV/Excel')
            </a>
        </div>

        {{-- Filter fields below --}}
        <div class="col-auto">
            <input name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                placeholder="@lang('Search…')">
        </div>

        <div class="col-auto">
            <select name="type" class="form-select form-select-sm">
                <option value="">@lang('All Types')</option>
                @foreach (\App\Http\Controllers\Admin\CustomerController::TYPES as $opt)
                    <option value="{{ $opt }}" @selected(request('type') === $opt)>
                        @lang($opt)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <select id="f_division" name="division_id" class="form-select form-select-sm">
                <option value="">@lang('All Divisions')</option>
                @foreach ($bd['divisions'] as $d)
                    <option value="{{ $d['id'] }}" @selected(request('division_id') == $d['id'])>{{ $d['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <select id="f_district" name="district_id" class="form-select form-select-sm">
                <option value="">@lang('All Districts')</option>
                {{-- JS populates options --}}
            </select>
        </div>

        <div class="col-auto">
            <select id="f_area" name="area_name" class="form-select form-select-sm">
                <option value="">@lang('All Areas')</option>
                {{-- JS populates options --}}
            </select>
        </div>

        <div class="col-auto">
            <button class="btn btn--sm btn--primary">@lang('Filter')</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn--sm btn--secondary ms-1">@lang('Reset')</a>
        </div>

    </form>


    @php
        $hasDivision = request()->filled('division_id');
        $hasDistrict = request()->filled('district_id');
        $hasArea = request()->filled('area_name');

        // Decide which word to show
        $level = $hasArea ? __('area') : ($hasDistrict ? __('district') : __('division'));
    @endphp

    @if ($hasDivision || $hasDistrict || $hasArea)
        <p class="text-muted mb-2">
            {{ __('Customers in selected') }} {{ $level }} :
            <strong>{{ $customers->total() }}</strong>
        </p>
    @endif


    {{-- ───── Table ───── --}}
    <div class="table-responsive--md table-responsive table-sm">
        <table class="table table--light style--two">
            <thead>
                <tr>
                    <th>@lang('Customer Id')</th>
                    <th>@lang('Customer Name')</th>
                    <th>@lang('Company Name')</th>
                    <th>@lang('Customer Type')</th>
                    <th>@lang('Contact Number')</th>
                    <th>@lang('Email')</th>
                    <th>@lang('Address')</th>
                    <th>Created By</th>
                    <th>@lang('Remarks')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td style="align-content: center;">{{ $customer->id }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->company }}</td>
                        <td>{{ $customer->customer_type }}</td>
                        <td>{{ $customer->contact_number }}</td>
                        <td>{{ $customer->email ?? '—' }}</td>

                        {{-- ── 2. show Division › District › Upazila just like detail page ── --}}
                        <td>
                            {{ $divName[$customer->division_id] ?? '-' }} &gt;
                            {{ $disName[$customer->district_id] ?? '-' }} &gt;
                            {{ is_numeric($customer->area_name) ? $upaName[$customer->area_name] ?? '-' : $customer->area_name }}
                        </td>
                        <td>{{ $customer->creator?->name ?? '—' }}</td>

                        <td>{{ Str::limit($customer->remarks, 20) }}</td>
                        <td>
                            <div class="btn-group gap-2">
                                @can('customers.edit')
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                        class="btn btn--xs btn--primary">
                                        <i class="la la-pencil"></i> @lang('Edit')
                                    </a>
                                @endcan

                                {{-- View --}}
                                @can('customers.view')
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn--xs btn--info">
                                        <i class="la la-eye"></i> @lang('View')
                                    </a>
                                @endcan

                                {{-- Delete --}}
                                @can('customers.delete')
                                    <form class="d-inline" action="{{ route('admin.customers.destroy', $customer) }}"
                                        method="POST" onsubmit="return confirm('@lang('Delete this record?')');">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn--xs btn--danger">
                                            <i class="la la-trash"></i> @lang('Delete')
                                        </button>
                                        <x-confirmation-modal />
                                    </form>
                                @endcan
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-3">@lang('No customers found.')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- pagination --}}
    @if ($customers->hasPages())
        <div class="card-footer py-3">
            {{ paginateLinks($customers) }}
        </div>
    @endif

    {{-- ───── Modal (Bootstrap + Alpine) ───── --}}
    <div id="customer-modal" class="modal fade" tabindex="-1" x-data="{ edit: null }" x-init="$watch('edit', v => Alpine.store('customerModal', { edit: v }))">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" x-text="edit ? '{{ __('Edit Customer') }}' : '{{ __('Add Customer') }}'"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST"
                    :action="edit ? '{{ url('admin/customers') }}/' + edit.id : '{{ route('admin.customers.store') }}'">
                    @csrf
                    <template x-if="edit"><input type="hidden" name="_method" value="put"></template>

                    <div class="modal-body">
                        {{-- Customer Name --}}
                        <div class="mb-3">
                            <label class="form-label">@lang('Customer Name')</label>
                            <input type="text" class="form-control" name="name" :value="edit?.name || ''" required>
                        </div>

                        {{-- Company --}}
                        <div class="mb-3">
                            <label class="form-label">@lang('Company Name')</label>
                            <input type="text" class="form-control" name="company" :value="edit?.company || ''">
                        </div>

                        {{-- Customer Type --}}
                        <div class="mb-3">
                            <label class="form-label">@lang('Customer Type')</label>
                            <select name="customer_type" class="form-select form-select-sm" required
                                {{-- ✓ pre‑select the current value when edit mode is active --}} x-bind:value="edit?.customer_type || ''">
                                <option value="">— @lang('Select') —</option>
                                <option value="Wholesale">@lang('Wholesale')</option>
                                <option value="Project">@lang('Project')</option>
                                <option value="Online">@lang('Online')</option>
                            </select>
                        </div>

                        {{-- Contact --}}
                        <div class="mb-3">
                            <label class="form-label">@lang('Contact Number')</label>
                            <input type="text" class="form-control" name="contact_number"
                                :value="edit?.contact_number || ''" required>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label">@lang('Email')</label>
                            <input type="email" class="form-control" name="email" :value="edit?.email || ''">
                        </div>

                        {{-- Cascading Division → District → Thana --}}
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="">Division</label>
                                <select id="divisionSelect" name="division_id" class="form-select form-select-sm"
                                    required></select>
                            </div>
                            <div class="col">
                                <label for="">District</label>
                                <select id="districtSelect" name="district_id" class="form-select form-select-sm"
                                    required></select>
                            </div>
                            <div class="col">
                                <label for="">Area</label>
                                <select id="thanaSelect" name="area_name" class="form-select form-select-sm"
                                    required></select>
                            </div>

                            <div class="col">
                                <label for="">Zip</label>
                                <input type="text" id="postcode" name="postcode"
                                    class="form-control form-control-sm" placeholder="@lang('Postcode')" readonly>
                                <small>0 if none</small>
                            </div>

                        </div>

                        {{-- Remarks --}}
                        <div class="mb-3">
                            <label class="form-label">@lang('Remarks')</label>
                            <textarea class="form-control" rows="2" name="remarks" x-text="edit?.remarks || ''"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn--secondary"
                            data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button class="btn btn--primary">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        /* make the PHP array we built at the very top available to JS */
        const BD_LOC = @json($bd);
        const POSTCODES = @json($postcodes);
    </script>
@endsection

@push('style')
    <style>
        .table th,
        .table td {
            font-size: 0.7rem;
            padding: 0.2rem 0.3rem;
            /* adjust smaller if needed */
        }

        .btn.btn--xs {
            padding: 0.15rem 0.4rem;
            font-size: 0.75rem;
        }

        .auto-hide-alert {
            top: 20px;
            right: 20px;
            z-index: 9999;
            position: fixed;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
    </style>
@endpush




@push('script')
    <script>
        (function($) {
            'use strict';

            /* -------------------------------------------------------------
               0. PHP → JS data
            ------------------------------------------------------------- */
            const BD_LOC = @json($bd);
            const POSTCODES = @json($postcodes);

            /* -------------------------------------------------------------
               1. short hands
            ------------------------------------------------------------- */
            const $div = $('#divisionSelect');
            const $dist = $('#districtSelect');
            const $thana = $('#thanaSelect');

            const option = (v, t) => `<option value="${v}">${t}</option>`;
            const empty = '<option value="">—</option>';

            /* -------------------------------------------------------------
               2. first fill – divisions
            ------------------------------------------------------------- */
            $.each(BD_LOC.divisions, (_, d) => $div.append(option(d.id, d.name)));

            /* -------------------------------------------------------------
               3. cascades
            ------------------------------------------------------------- */
            $div.on('change', function() {
                const id = +this.value || 0;
                $dist.html(empty);
                $thana.html(empty);

                if (!id) return;

                BD_LOC.districts
                    .filter(d => +d.division_id === id)
                    .forEach(d => $dist.append(option(d.id, d.name)));
            });

            $dist.on('change', function() {
                const id = +this.value || 0;
                $thana.html(empty);

                if (!id) return;

                const areas = id === 1 /* Dhaka city special-case */ ?
                    BD_LOC.dhaka.map(a => ({
                        value: a.name,
                        name: a.name
                    })) :
                    BD_LOC.upazilas
                    .filter(u => +u.district_id === id)
                    .map(u => ({
                        value: u.name,
                        name: u.name
                    })); // value = plain text

                areas.sort((a, b) => a.name.localeCompare(b.name))
                    .forEach(a => $thana.append(option(a.value, a.name)));
            });

            /* -------------------------------------------------------------
               4. thana → postcode
            ------------------------------------------------------------- */
            $('#thanaSelect').on('change', function() {

                const areaName = $(this).val();
                const districtId = $dist.val();

                const hits = POSTCODES.filter(p =>
                    +p.district_id === +districtId &&
                    (
                        p.upazila.toLowerCase() === areaName.toLowerCase() ||
                        p.postOffice.toLowerCase() === areaName.toLowerCase()
                    )
                );

                if (hits.length === 1) {
                    $('#postcode').replaceWith(`
                <input  type="text"
                        id="postcode"
                        name="postcode"
                        class="form-control form-control-sm"
                        value="${hits[0].postCode}"
                        readonly>
            `);
                } else if (hits.length > 1) {
                    const $sel = $(
                        '<select id="postcode" name="postcode" class="form-select form-select-sm" required>'
                    );
                    $sel.append('<option value="">—</option>');
                    hits.forEach(p =>
                        $sel.append(`<option value="${p.postCode}">
                                ${p.postOffice} (${p.postCode})
                             </option>`)
                    );
                    $('#postcode').replaceWith($sel);
                } else {
                    $('#postcode').replaceWith(`
                <input  type="text"
                        id="postcode"
                        name="postcode"
                        class="form-control form-control-sm"
                        required>
            `);
                }
            });


            /* --------------------------------------------------
               0-bis.  wire the buttons  (Add & Edit)
            -------------------------------------------------- */
            // 1. Edit buttons
            $(document).on('click', '[data-edit]', function() {
                Alpine.store('customerModal', {
                    edit: $(this).data('edit')
                });
                $('#customer-modal').modal('show');
            });

            // 2. Add-Customer button
            $(document).on('click', '[data-bs-target="#customer-modal"]:not([data-edit])', function() {
                Alpine.store('customerModal', {
                    edit: null
                });
            });

            /* -------------------------------------------------------------
               5. modal-open: pre-select when editing
            ------------------------------------------------------------- */
            $('#customer-modal').on('show.bs.modal', function() {
                $div.val('');
                $dist.html(empty);
                $thana.html(empty);

                const edit = Alpine.store('customerModal')?.edit;
                if (!edit) return; // “Add” mode – nothing to pre-fill

                $div.val(edit.division_id).trigger('change');

                /* wait 50 ms for the districts to be inserted … */
                setTimeout(() => {
                    $dist.val(edit.district_id).trigger('change');

                    /* … then another 50 ms for the areas */
                    setTimeout(() => {
                        $thana.val(edit.area_name); // ⬅️   just the val(), no 50 here
                    }, 50);
                }, 50);
            });
            /* -----------------------------------------------------------------
       A. populate district + area selects on page load
    ----------------------------------------------------------------- */
            (function initLocationFilters() {
                const $fDiv = $('#f_division');
                const $fDis = $('#f_district');
                const $fArea = $('#f_area');

                const option = (v, t) => `<option value="${v}">${t}</option>`;

                // 1️⃣ build district list on division change
                $fDiv.on('change', function() {
                    const id = +this.value || 0;

                    $fDis.html('<option value="">@lang('All Districts')</option>');
                    $fArea.html('<option value="">@lang('All Areas')</option>');

                    if (!id) return;

                    BD_LOC.districts
                        .filter(d => +d.division_id === id)
                        .forEach(d => $fDis.append(option(d.id, d.name)));

                    // keep previously‑selected district (if any)
                    $fDis.val('{{ request('district_id') }}').trigger('change');
                });

                // 2️⃣ build area list on district change
                $fDis.on('change', function() {
                    const id = +this.value || 0;

                    $fArea.html('<option value="">@lang('All Areas')</option>');
                    if (!id) return;

                    const areas = id === 1 ?
                        BD_LOC.dhaka.map(a => ({
                            value: a.name,
                            name: a.name
                        })) :
                        BD_LOC.upazilas
                        .filter(u => +u.district_id === id)
                        .map(u => ({
                            value: u.name,
                            name: u.name
                        }));

                    areas.sort((a, b) => a.name.localeCompare(b.name))
                        .forEach(a => $fArea.append(option(a.value, a.name)));

                    // keep previously‑selected area (if any)
                    $fArea.val('{{ request('area_name') }}');
                });

                // initialise on page load
                if ('{{ request('division_id') }}') {
                    $fDiv.trigger('change');
                }
            })();
        })(jQuery);
    </script>
    <script>
        (function() {
            setTimeout(function() {
                $('.auto-hide-alert').fadeOut('slow');
            }, 3000);
        })();
    </script>
@endpush

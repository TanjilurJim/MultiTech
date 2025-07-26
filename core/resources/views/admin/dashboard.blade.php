@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card summary-card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Summary')</h5>
                    {{-- <div class="row g-0">
                        <div class="col-xl-3 col-sm-6">
                            <div class="p-3 border-card h-100">
                                <small class="text-muted">@lang('Total Sales')</small>
                                <h6>{{ showAmount($deposit['total_deposit_amount']) }}</h6>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-6">
                            <div class="p-3 border-card h-100">
                                <small class="text-muted">@lang('Payment Pending')</small>
                                <h6>{{ showAmount($deposit['total_deposit_pending']) }}</h6>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-6">
                            <div class="p-3 border-card h-100">
                                <small class="text-muted">@lang('Rejected Payment')</small>
                                <h6>{{ $deposit['total_deposit_rejected'] }}</h6>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-6">
                            <div class="p-3 border-card h-100">
                                <small class="text-muted">@lang('Payment Charge')</small>
                                <h5>{{ showAmount($deposit['total_deposit_charge']) }}</h5>
                            </div>
                        </div>
                    </div> --}}
                    {{-- ==== KPI CARDS ========================================================= --}}
                    <div class="row g-3 mb-4">

                        @php
                            $cardIcons = ['las la-users', 'las la-phone', 'las la-shopping-bag', 'las la-user-shield'];
                            $labels = ['Customers', 'Follow-Ups (Last 30days)',  'Users'];
                        @endphp

                        @foreach ($kpis as $key => $value)
                            @continue(is_null($value)) {{-- hide Admin-users card for non-SAs --}}
                            <div class="col-md-3 col-sm-6">
                                <div class="p-3 border-card text-center h-100">
                                    <i class="{{ $cardIcons[$loop->index] }} fs-2 mb-2 text-primary"></i>
                                    <h3 class="mb-0">{{ $value }}</h3>
                                    <small class="text-muted">{{ __($labels[$loop->index]) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ==== LATEST FOLLOW-UPS TABLE ========================================== --}}
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="mb-0">Latest Follow-Ups</h5>
                            @can('followup_logs.view')
                                <a href="{{ route('admin.followups.index') }}" class="btn btn-sm btn-outline-secondary">
                                    View All
                                </a>
                            @endcan
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>By</th>
                                        <th>Contacted</th>
                                        <th>Potential</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($latestFollowUps as $f)
                                        <tr>
                                            <td>{{ $f->contact_date->format('d-M') }}</td>
                                            <td>{{ $f->admin->name }}</td>
                                            <td>{{ $f->customers_contacted }}</td>
                                            <td>{{ $f->potential_customers }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">– No data –</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ==== TOP PERFORMERS ==================================================== --}}
                    @role('super-admin')
                        @if ($topPerformers->count())
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Top Performers (30 days)</h5>
                                </div>
                                <ul class="list-group list-group-flush">
                                    @foreach ($topPerformers as $tp)
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>{{ $tp->admin->name }}</span>
                                            <span class="badge bg-success">{{ $tp->total }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endrole

                </div>
            </div>
        </div>
    @endsection

    @push('script-lib')
        <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
        <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
    @endpush

    @push('style-lib')
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
    @endpush

    @push('script')
        <script>
            "use strict";

            const start = moment().subtract(14, 'days');
            const end = moment();

            const dateRangeOptions = {
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            }

            const changeDatePickerText = (element, startDate, endDate) => {
                $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
            }

            
        </script>
    @endpush
    @push('style')
        <style>
            .apexcharts-menu {
                min-width: 120px !important;
            }

            .custom-list-group .list-group-item {
                padding: 16px 0px;
            }

            .account-widget .widget-two,
            .counting-widget .widget-two {
                border: 1px solid #eee !important;
                box-shadow: none !important;
            }

            .summary-card .border-card {
                box-shadow: 0 0 0 1px #eee;
                background-color: white
            }

            .counting-widget .widget-two {
                padding: 10px;
            }

            .counting-widget .widget-two__icon {
                width: 46px;
                height: 46px;
            }

            .counting-widget .widget-two__icon i {
                font-size: 32px;
            }

            .counting-widget .widget-two__content {
                width: 100%;
                flex: 1;
            }

            .counting-widget .widget-two__content h3 {
                font-size: 1.125rem;
            }

            .counting-widget .widget-two__content p {
                font-size: 0.8rem;
            }
        </style>
    @endpush



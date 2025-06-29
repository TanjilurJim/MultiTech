@extends('admin.layouts.app')

@section('panel')
{{-- Summary Box --}}
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Last 30 Days – Company-wide Summary</h5>

        <a href="{{ route('followups.report', ['download' => 1]) }}"
           class="btn btn-sm btn-outline-success">
           Export Excel
        </a>
    </div>
    <div class="card-body">
        <p>Total customers contacted: <strong>{{ $stats->contacted }}</strong></p>
        <p>Total potential customers: <strong>{{ $stats->potential }}</strong></p>
    </div>
</div>

{{-- Per-Employee Breakdown --}}
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Employee Breakdown</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Contacted</th>
                    <th>Potential</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($summaries as $row)
                    <tr>
                        <td>{{ $row->user->name }}</td>
                        <td>{{ $row->contacted }}</td>
                        <td>{{ $row->potential }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

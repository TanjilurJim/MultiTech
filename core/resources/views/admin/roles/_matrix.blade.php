<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th>Permission Name</th>
            @foreach ($abilities as $ability)
                <th class="text-center text-capitalize">{{ $ability }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach ($permissions as $module => $perms)
            <tr>
                <td class="fw-semibold">{{ Str::headline($module) }}</td>

                @foreach ($abilities as $ability)
                    @php
                        $perm = $perms->firstWhere('name', "$module.$ability");
                    @endphp
                    <td class="text-center">
                        @if ($perm)
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                @if (isset($rolePermIds) && in_array($perm->id, $rolePermIds)) checked @endif>
                            
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

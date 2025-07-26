{{-- resources/views/admin/admin-users/_form.blade.php --}}
@csrf
<div class="row g-3">

    {{-- ───── basic profile fields ───── --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Name</label>
        <input name="name" type="text" class="form-control" value="{{ old('name', $admin->name ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email', $admin->email ?? '') }}"
            required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Username</label>
        <input name="username" type="text" class="form-control" value="{{ old('username', $admin->username ?? '') }}"
            required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Password
            @isset($admin)
                <small class="text-muted">(leave blank = keep)</small>
            @endisset
        </label>
        <input name="password" type="password" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Confirm Password</label>
        <input name="password_confirmation" type="password" class="form-control">
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" value="1" name="is_active" id="isActiveSwitch"
                @checked(!isset($admin) || $admin->is_active)>
            <label class="form-check-label fw-semibold" for="isActiveSwitch">
                Active
            </label>
        </div>
    </div>

    <hr class="my-3">

    {{-- ───── Roles (names) ───── --}}
    <div class="col-12">
        <label class="form-label fw-semibold d-block">Assign Role(s)</label>

        @foreach ($roles as $roleName)
            <div class="form-check form-check-inline mb-2">
                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $roleName }}"
                    @checked(isset($admin) && $admin->hasRole($roleName))>
                <label class="form-check-label">
                    {{ ucwords(str_replace('-', ' ', $roleName)) }}
                </label>
            </div>
        @endforeach
    </div>

    {{-- ───── Extra permissions (names) ───── --}}
    <div class="col-12">
        <label class="form-label fw-semibold d-block">Extra Permissions</label>

        <div class="border rounded p-2" style="max-height:220px; overflow-y:auto;">
            @foreach ($perms as $permName)
                <div class="form-check form-check-inline mb-1">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permName }}"
                        @checked(isset($admin) && $admin->hasPermissionTo($permName))>
                    <label class="form-check-label">
                        {{ ucwords(str_replace('-', ' ', $permName)) }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <hr class="my-3">

    <div class="col-12">
        <label class="form-label fw-semibold d-block">Assign Area(s)</label>

        <div id="area-assignments-container">
            {{-- JavaScript will add rows here dynamically --}}
        </div>

        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addAreaRow()">
            <i class="las la-plus"></i> Add Area
        </button>
    </div>

    <template id="area-row-template">
        <div class="area-row border p-2 rounded mb-2">
            <div class="row g-2">
                <div class="col-md-4">
                    <select class="form-select division-select" required>
                        <option value="">Select Division</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select district-select" required>
                        <option value="">Select District</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select area-select" required>
                        <option value="">Select Area / Thana</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-danger"
                        onclick="this.closest('.area-row').remove()">&times;</button>
                </div>
            </div>
        </div>
    </template>



</div>
<script>
    const divisions = @json($locationData['divisions']);
    const districts = @json($locationData['districts']);
    const upazilas = @json($locationData['upazilas']);
    const dhaka = @json($locationData['dhaka']);

    let areaRowIndex = 0;

    function addAreaRow(selected = {}) {
        const tmpl = document.getElementById('area-row-template').content.cloneNode(true);
        const row = tmpl.querySelector('.area-row');

        // Set the correct name attributes
        row.querySelector('.division-select').setAttribute('name', `areas[${areaRowIndex}][division_id]`);
        row.querySelector('.district-select').setAttribute('name', `areas[${areaRowIndex}][district_id]`);
        row.querySelector('.area-select').setAttribute('name', `areas[${areaRowIndex}][area_name]`);
        areaRowIndex++;

        const divSel = row.querySelector('.division-select');
        const distSel = row.querySelector('.district-select');
        const areaSel = row.querySelector('.area-select');

        // Populate divisions
        divisions.forEach(d => {
            divSel.insertAdjacentHTML('beforeend', `<option value="${d.id}">${d.name}</option>`);
        });

        // Set initial values if passed (for edit)
        if (selected.division_id) divSel.value = selected.division_id;

        divSel.addEventListener('change', () => {
            distSel.innerHTML = '<option value="">Select District</option>';
            areaSel.innerHTML = '<option value="">Select Area / Thana</option>';

            districts.filter(x => x.division_id == divSel.value)
                .forEach(d => {
                    distSel.insertAdjacentHTML('beforeend', `<option value="${d.id}">${d.name}</option>`);
                });

            // If coming from edit, pre-select district
            if (selected.district_id) {
                distSel.value = selected.district_id;
                distSel.dispatchEvent(new Event('change'));
            }
        });

        distSel.addEventListener('change', () => {
            areaSel.innerHTML = '<option value="">Select Area / Thana</option>';

            if (!distSel.value) return;

            const list = distSel.value == 1 ?
                dhaka.map(a => ({
                    name: a.name
                })) :
                upazilas.filter(u => u.district_id == distSel.value);

            list.sort((a, b) => a.name.localeCompare(b.name))
                .forEach(a => {
                    areaSel.insertAdjacentHTML('beforeend', `<option value="${a.name}">${a.name}</option>`);
                });

            // If coming from edit, pre-select area
            if (selected.area_name) {
                areaSel.value = selected.area_name;
            }
        });

        // If editing, trigger the cascading for initial selection
        if (selected.division_id) divSel.dispatchEvent(new Event('change'));

        document.getElementById('area-assignments-container').appendChild(row);
    }

    // ---- For edit page: load existing assignments ----
    document.addEventListener('DOMContentLoaded', () => {
        @isset($admin)
            @foreach ($admin->areaAssignments as $a)
                addAreaRow({
                    division_id: '{{ $a->division_id }}',
                    district_id: '{{ $a->district_id }}',
                    area_name: '{{ $a->area_name }}'
                });
            @endforeach
        @endisset
    });
</script>

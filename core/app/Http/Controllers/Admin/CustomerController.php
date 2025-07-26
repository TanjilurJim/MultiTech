<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    //
    use AuthorizesRequests;

    /**
     * Allowed customer types.
     */
    public const TYPES = [
        'Wholesale',
        'Project',
        'Online',
        // Add more types as needed
    ];

    public function __construct()
    {
        /*
         * One closure-based middleware for the whole controller:
         *   – maps the current action name to the correct policy ability
         *   – runs $this->authorize(...) for you
         */
        $this->middleware(function ($request, $next) {
            $method  = $request->route()->getActionMethod();   // e.g. index, show, update …
            $ability = $this->abilityMap()[$method] ?? null;   // e.g. viewAny, view, update …

            if ($ability) {
                // If the route contains {customer} → get the model instance
                $model = $request->route('customer');

                // viewAny receives only the class name
                $subject = $ability === 'viewAny' ? Customer::class : $model;

                $this->authorize($ability, $subject);
            }

            return $next($request);
        });
    }

    /**
     * Map controller method ➜ policy ability.
     * Same defaults Laravel uses internally.
     */
    protected function abilityMap(): array
    {
        return [
            'index'   => 'viewAny',
            'show'    => 'view',
            'edit'    => 'update',
            'update'  => 'update',
            'destroy' => 'delete',
            // add more if you create store/create etc.
        ];
    }


    public function index(Request $request)
    {
        $bd = getBangladeshLocationData(); 
        $query = Customer::visibleTo(auth('admin')->user())
        ->with('creator');

        // search/filter
        if ($term = $request->query('q')) {
            $query->where(function ($q) use ($term, $bd) {
                // existing clauses …
                $q->where('name', 'like', "%$term%")
                    ->orWhere('company', 'like', "%$term%")
                    ->orWhere('contact_number', 'like', "%$term%");

                // NEW – match division / district / area names
                $divIds = collect($bd['divisions'])
                    ->filter(fn($d) => str_contains(strtolower($d['name']), strtolower($term)))
                    ->pluck('id');
                $disIds = collect($bd['districts'])
                    ->filter(fn($d) => str_contains(strtolower($d['name']), strtolower($term)))
                    ->pluck('id');

                if ($divIds->isNotEmpty())  $q->orWhereIn('division_id', $divIds);
                if ($disIds->isNotEmpty())  $q->orWhereIn('district_id', $disIds);

                // plain string match for area_name (Elephant Road, etc.)
                $q->orWhere('area_name', 'like', "%$term%");
            });
        }

        // type filter ---------------------------------------------------
        if ($type = $request->query('type')) {
            // silent‑fail if an invalid value is passed
            if (in_array($type, self::TYPES, true)) {
                $query->where('customer_type', $type);
            }
        }

        // location filters (division → district → area)
        // -----------------------------------------------------------------
        if ($div = $request->query('division_id')) {
            $query->where('division_id', $div);
        }

        if ($dis = $request->query('district_id')) {
            $query->where('district_id', $dis);
        }

        if ($area = $request->query('area_name')) {
            $query->where('area_name', $area);
        }

        $pageTitle = 'Customer Database';

        $customers = $query->latest()
            ->paginate(7)
            ->appends($request->only(['q', 'type', 'division_id', 'district_id', 'area_name']));

        $bd        = getBangladeshLocationData();          // ① send to view
        $term      = $term ?? null;                        // keep old vars

        return view('admin.customers.index', compact(
            'customers',
            'term',
            'type',
            'bd',                                           // new
            'pageTitle'
        ));
    }


    public function edit(Customer $customer)
    {

        $pageTitle = 'Customer Information Edit';
        $bd        = getBangladeshLocationData();
        $postcodes = json_decode(
            file_get_contents(resource_path('data/bd-postcodes.json')),
            true
        )['postcodes'];

        return view('admin.customers.edit', compact('customer', 'bd', 'postcodes', 'pageTitle'));
    }

    public function show(Customer $customer)
    {
        $pageTitle = 'Customer Information';
        $bd = getBangladeshLocationData();
        $divName = collect($bd['divisions'])->pluck('name', 'id');
        $disName = collect($bd['districts'])->pluck('name', 'id');
        $upaName = collect($bd['upazilas'])->pluck('name', 'id');
        return view('admin.customers.show', compact('customer', 'divName', 'disName', 'upaName', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string',
            'company'        => 'nullable|string',
            'contact_number' => 'required|string',
            'email'          => 'nullable|email',
            'division_id'    => 'required|integer',
            'district_id'    => 'required|integer',
            // 'thana_id'       => 'required',
            'area_name' => 'required|string',
            'postcode'       => 'nullable|string',
            'customer_type'  => ['required', Rule::in(['Wholesale', 'Project', 'Online'])],
            'remarks'        => 'nullable|string',
        ]);

        Customer::create($data + [
        'customer_type' => $data['customer_type'] ?? 'Wholesale',
        'created_by'    => auth('admin')->id(),
    ]);
        return back()->with('success', 'Customer added.');
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'           => 'required|string',
            'company'        => 'nullable|string',
            'contact_number' => 'required|string',
            'email'          => 'nullable|email',
            'division_id'    => 'required|integer',
            'district_id'    => 'required|integer',
            // 'thana_id'       => 'required',
            'area_name' => 'required|string',
            'postcode'       => 'nullable|string',
            'customer_type'  => ['required', Rule::in(['Wholesale', 'Project', 'Online'])],
            'remarks'        => 'nullable|string',
        ]);

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Customer removed.');
    }

    public function export()
    {
        $admin = auth('admin')->user();
        return Excel::download(new CustomerExport($admin), 'customers.xlsx');
    }
}

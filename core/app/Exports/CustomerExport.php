<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    protected $divisions;
    protected $districts;
    protected $admin;  
    
    public function __construct($admin)
    {
        $bd = getBangladeshLocationData();
        $this->divisions = collect($bd['divisions'])->pluck('name', 'id');
        $this->districts = collect($bd['districts'])->pluck('name', 'id');
        $this->admin     = $admin;   // save for use in collection()
    }

    // public function __construct()
    // {
    //     $bd = getBangladeshLocationData();
    //     $this->divisions = collect($bd['divisions'])->pluck('name', 'id');
    //     $this->districts = collect($bd['districts'])->pluck('name', 'id');
    // }

    public function collection()
    {
        return Customer::visibleTo($this->admin)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Company',
            'Contact',
            'Email',
            'Division',
            'District',
            'Area',
            'Postcode',
            'Remarks'
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->company,
            $customer->contact_number,
            $customer->email,
            $this->divisions[$customer->division_id] ?? '-',
            $this->districts[$customer->district_id] ?? '-',
            $customer->area_name,
            $customer->postcode,
            $customer->remarks
        ];
    }
}

<?php

namespace App\Policies;

use App\Models\Admin;    // guard user model
use App\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /** List view (index) — we allow it; the query itself is filtered by scope */

    public function before(Admin $user, $ability)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    public function viewAny(Admin $user): bool
    {
        return true;
    }

    /** Single-row view — must belong to one of the admin’s areas */
    public function view(Admin $user, Customer $customer): bool
    {
        return Customer::visibleTo($user)
            ->whereKey($customer->id)
            ->exists();
    }

    /** Re-use the same check for update / delete / whatever */
    public function update(Admin $user, Customer $customer): bool
    {
        return $this->view($user, $customer);
    }

    public function delete(Admin $user, Customer $customer): bool
    {
        return $this->view($user, $customer);
    }
}

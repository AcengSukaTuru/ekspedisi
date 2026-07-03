<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Scoped in controller
    }

    public function view(User $user, Payment $payment): bool
    {
        $payment->loadMissing('shipment');

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer()) {
            return $payment->shipment && $payment->shipment->customer && $payment->shipment->customer->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    public function update(User $user, Payment $payment): bool
    {
        return $this->view($user, $payment) && $user->isCustomer();
    }

    public function verify(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }
}

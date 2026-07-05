<?php

namespace App\Policies;

use App\Models\DeliveryJob;
use App\Models\User;

class DeliveryJobPolicy
{
    public function take(User $user, DeliveryJob $job): bool
    {
        return $user->currentActiveRole() === 'Driver' && $job->status === 'available';
    }

    public function complete(User $user, DeliveryJob $job): bool
    {
        return $user->currentActiveRole() === 'Driver'
            && $job->driver_id === $user->id
            && $job->status === 'taken';
    }
}

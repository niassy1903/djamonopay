<?php

namespace App\Actions\Jetstream;

use App\Models\Users2;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(Users2 $user): void
    {
        $user->tokens->each->delete();
        $user->delete();
    }
}

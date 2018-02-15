<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /* 用户更新授权 */
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    /* 删除用户 授权 */
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }
}
